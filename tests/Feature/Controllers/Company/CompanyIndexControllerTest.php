<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Company;

use App\Models\Company;
use App\Models\User;
use App\Services\CompanyAccessService;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CompanyIndexControllerTest extends TestCase
{
    #[Test]
    public function it_can_get_index_company(): void
    {
        $this->withoutExceptionHandling();

        $company = Company::factory()->create();

        $response = $this->get('/company');

        $response->assertOk();
        $response->assertViewIs('company.index');
        $response->assertSee(__('Name'));
        $response->assertSee(__('E-mail'));
        $response->assertSee(__('Country'));
        $response->assertSee($company->name);
        $response->assertSee($company->email);
        $response->assertSee($company->country->name);
    }

    #[Test]
    public function it_lists_only_active_companies_by_default(): void
    {
        $activeCompany = Company::factory()->create();
        $deletedCompany = Company::factory()->create();
        $deletedCompany->delete();

        $response = $this->get('/company');

        $response->assertOk();
        $response->assertSee($activeCompany->name);
        $response->assertDontSee($deletedCompany->name);
    }

    #[Test]
    public function it_lists_active_and_deleted_companies_with_all_filter(): void
    {
        $activeCompany = Company::factory()->create();
        $deletedCompany = Company::factory()->create();
        $deletedCompany->delete();

        $response = $this->get('/company?filter=all');

        $response->assertOk();
        $response->assertSee($activeCompany->name);
        $response->assertSee($deletedCompany->name);
    }

    #[Test]
    public function it_filters_only_active_companies(): void
    {
        $activeCompany = Company::factory()->create();
        $deletedCompany = Company::factory()->create();
        $deletedCompany->delete();

        $response = $this->get('/company?filter=active');

        $response->assertOk();
        $response->assertSee($activeCompany->name);
        $response->assertDontSee($deletedCompany->name);
    }

    #[Test]
    public function it_filters_only_deleted_companies(): void
    {
        $activeCompany = Company::factory()->create();
        $deletedCompany = Company::factory()->create();
        $deletedCompany->delete();

        $response = $this->get('/company?filter=deleted');

        $response->assertOk();
        $response->assertSee($deletedCompany->name);
        $response->assertDontSee($activeCompany->name);
    }

    #[Test]
    public function it_rejects_an_invalid_filter(): void
    {
        $response = $this->get('/company?filter=invalid');

        $response->assertSessionHasErrors('filter');
    }

    #[Test]
    public function super_admin_sees_the_companies_quota_with_used_client_companies(): void
    {
        $parentCompany = Company::factory()->withQuota(5)->create();
        Company::factory()->clientOf($parentCompany)->count(2)->create();
        Company::factory()->clientOf($parentCompany)->create()->delete();

        $response = $this->get('/company');

        $response->assertOk();
        $response->assertSee(__('Companies quota'));
        $response->assertSee('3 / 5');
        $response->assertDontSee('0 / 0');
    }

    #[Test]
    public function the_company_list_shows_which_company_manages_each_client_company(): void
    {
        $parentCompany = Company::factory()->withQuota(2)->create(['name' => 'Parent company name']);
        $clientCompany = Company::factory()->clientOf($parentCompany)->create();

        $response = $this->get('/company');

        $response->assertOk();
        $response->assertSee(__('Managed by'));
        $this->assertMatchesRegularExpression(
            '/'.preg_quote(e($clientCompany->name), '/').'.*?<td>Parent company name<\/td>/s',
            $response->getContent()
        );
    }

    #[Test]
    public function company_admin_only_sees_their_company(): void
    {
        $companyAdmin = $this->createCompanyAdmin();
        $otherCompany = Company::factory()->create();

        $this->actingAs($companyAdmin)->get('/company')
            ->assertOk()
            ->assertSee($companyAdmin->company->name)
            ->assertDontSee($otherCompany->name)
            ->assertSee(__('Companies quota'))
            ->assertDontSee('name="filter"', false);
    }

    #[Test]
    public function multi_company_user_sees_their_default_and_extra_companies(): void
    {
        $multiCompanyUser = $this->createCompanyAdmin();
        $clientCompany = Company::factory()->clientOf($multiCompanyUser->company)->create();
        $otherCompany = Company::factory()->create();
        app(CompanyAccessService::class)->grant($multiCompanyUser, $clientCompany);

        $this->actingAs($multiCompanyUser->fresh())->get('/company')
            ->assertOk()
            ->assertSee($multiCompanyUser->company->name)
            ->assertSee($clientCompany->name)
            ->assertDontSee($otherCompany->name)
            ->assertSee('href="'.url('/company/update/'.$multiCompanyUser->company_id).'"', false)
            ->assertSee('href="'.url('/company/update/'.$clientCompany->id).'"', false)
            ->assertDontSee('href="'.url('/company/update/'.$otherCompany->id).'"', false);
    }

    #[Test]
    public function company_admin_cannot_filter_deleted_companies(): void
    {
        $this->actingAs($this->createCompanyAdmin())->get('/company?filter=all')
            ->assertSessionHasErrors('filter');
    }

    #[Test]
    public function other_roles_cannot_open_the_company_list(): void
    {
        $this->seed([RoleSeeder::class, PermissionSeeder::class]);
        $support = User::factory()->create();
        $support->assignRole('Support');

        $this->actingAs($support)->get('/company')->assertForbidden();
    }

    #[Test]
    public function company_admin_can_only_edit_the_companies_they_can_access(): void
    {
        $companyAdmin = $this->createCompanyAdmin();
        $clientCompany = Company::factory()->create();
        $otherCompany = Company::factory()->create();
        app(CompanyAccessService::class)->grant($companyAdmin, $clientCompany);
        $companyAdmin->refresh();

        $this->actingAs($companyAdmin)->get('/company/update/'.$companyAdmin->company_id)->assertOk();
        $this->actingAs($companyAdmin)->get('/company/update/'.$clientCompany->id)->assertOk();
        $this->actingAs($companyAdmin)->get('/company/update/'.$otherCompany->id)->assertForbidden();

        $this->actingAs($companyAdmin)->post('/company/save', ['id' => $clientCompany->id, 'name' => 'Client company renamed', 'currency' => 'USD'])
            ->assertRedirect(route('company.index'));
        $this->actingAs($companyAdmin)->post('/company/save', ['id' => $otherCompany->id, 'name' => 'Not allowed', 'currency' => 'USD'])
            ->assertForbidden();

        $this->assertDatabaseHas('company', ['id' => $clientCompany->id, 'name' => 'Client company renamed']);
        $this->assertDatabaseMissing('company', ['name' => 'Not allowed']);
    }

    private function createCompanyAdmin(): User
    {
        $this->seed([RoleSeeder::class, PermissionSeeder::class]);
        $companyAdmin = User::factory()->create();
        $companyAdmin->assignRole('CompanyAdmin');

        return $companyAdmin;
    }
}
