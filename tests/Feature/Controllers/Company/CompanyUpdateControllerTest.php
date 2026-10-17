<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Company;

use App\Models\Company;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CompanyUpdateControllerTest extends TestCase
{
    #[Test]
    public function it_can_update_company(): void
    {
        $company = Company::factory()->create();

        $response = $this->get('/company/update/'.$company->id);

        $response->assertOk();
        $response->assertViewIs('company.company');
        $response->assertSee(__('Name'));
        $response->assertSee(__('E-mail'));
        $response->assertSee(__('Country'));
        $response->assertSee($company->name);
        $response->assertSee($company->email);
        $response->assertSee($company->country->name);
    }

    #[Test]
    public function it_can_open_a_deleted_company_for_update(): void
    {
        $company = Company::factory()->create();
        $company->delete();

        $response = $this->get('/company/update/'.$company->id);

        $response->assertOk();
        $response->assertViewIs('company.company');
        $response->assertSee($company->name);
    }

    #[Test]
    public function it_returns_not_found_for_a_missing_company(): void
    {
        $response = $this->get('/company/update/999999');

        $response->assertNotFound();
    }

    #[Test]
    public function super_admin_sees_the_status_select(): void
    {
        $company = Company::factory()->create();

        $response = $this->get('/company/update/'.$company->id);

        $response->assertOk();
        $response->assertSee('name="status"', false);
    }

    #[Test]
    public function non_super_admin_does_not_see_the_status_select(): void
    {
        $companyAdmin = User::factory()->create();
        $companyAdmin->assignRole('User');
        $companyAdmin->givePermissionTo(Permission::create(['name' => 'update company', 'guard_name' => 'web']));

        $response = $this->actingAs($companyAdmin)->get('/company/update/'.$companyAdmin->company_id);

        $response->assertOk();
        $response->assertDontSee('name="status"', false);
    }
}
