<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\User;

use App\Models\Company;
use App\Models\User;
use App\Services\CompanyAccessService;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class UserListControllerTest extends TestCase
{
    #[Test]
    public function it_can_list_users(): void
    {
        $user = User::factory()->create();

        $response = $this->get('/user');

        $response->assertOk();
        $response->assertViewIs('user.index');
        $response->assertSee($user->email);
    }

    #[Test]
    public function it_lists_users_of_a_company_deleted_without_cascading(): void
    {
        $user = User::factory()->create();
        Company::find($user->company_id)->deleteQuietly();

        $response = $this->get('/user');

        $response->assertOk();
        $response->assertSee($user->email);
    }

    #[Test]
    public function it_shows_the_name_of_a_deleted_company(): void
    {
        $user = User::factory()->create();
        $company = Company::find($user->company_id);
        $company->deleteQuietly();

        $response = $this->get('/user');

        $response->assertOk();
        $response->assertSee($company->name);
    }

    #[Test]
    public function non_super_admin_only_sees_users_of_own_company(): void
    {
        $companyUser = User::factory()->create();
        $companyUser->assignRole('User');
        $companyUser->givePermissionTo([
            Permission::create(['name' => 'read user', 'guard_name' => 'web']),
            Permission::create(['name' => 'create company', 'guard_name' => 'web']),
        ]);
        $colleague = User::factory()->create(['company_id' => $companyUser->company_id]);
        $otherCompanyUser = User::factory()->create();

        $response = $this->actingAs($companyUser)->get('/user');

        $response->assertOk();
        $response->assertSee($colleague->email);
        $response->assertDontSee($otherCompanyUser->email);
    }

    #[Test]
    public function super_admin_sees_users_of_all_companies(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $response = $this->get('/user');

        $response->assertOk();
        $response->assertSee($userA->email);
        $response->assertSee($userB->email);
    }

    #[Test]
    public function users_of_a_deleted_company_are_not_listed(): void
    {
        $user = User::factory()->create();
        Company::find($user->company_id)->delete();

        $response = $this->get('/user');

        $response->assertOk();
        $response->assertDontSee($user->email);
    }

    #[Test]
    public function super_admin_sees_how_many_users_have_multi_company_access(): void
    {
        $this->seed([RoleSeeder::class, PermissionSeeder::class]);
        $multiCompanyUser = User::factory()->create();
        app(CompanyAccessService::class)->grant($multiCompanyUser, Company::factory()->create());
        User::factory()->create()->assignRole(User::MULTI_COMPANY_ADMIN);

        $this->get('/user')
            ->assertOk()
            ->assertSee(__('Users with access to several companies: :count', ['count' => 1]))
            ->assertSee(__('MultiCompanyAdmin: :count', ['count' => 1]));
    }

    #[Test]
    public function a_multi_company_user_is_listed_and_managed_by_their_default_company_only(): void
    {
        $this->seed([RoleSeeder::class, PermissionSeeder::class]);
        $parentCompany = Company::factory()->create();
        $clientCompany = Company::factory()->create();
        $multiCompanyUser = User::factory()->create(['company_id' => $parentCompany->id]);
        app(CompanyAccessService::class)->grant($multiCompanyUser, $clientCompany);
        $multiCompanyUser->refresh()->update(['company_id' => $clientCompany->id]);

        $parentCompanyAdmin = User::factory()->create(['company_id' => $parentCompany->id]);
        $parentCompanyAdmin->assignRole('CompanyAdmin');
        $clientCompanyAdmin = User::factory()->create(['company_id' => $clientCompany->id]);
        $clientCompanyAdmin->assignRole('CompanyAdmin');

        $this->actingAs($parentCompanyAdmin)->get('/user')
            ->assertOk()
            ->assertSee($multiCompanyUser->email)
            ->assertDontSee('id="multi-company-summary"', false);
        $this->actingAs($clientCompanyAdmin)->get('/user')
            ->assertOk()
            ->assertDontSee($multiCompanyUser->email);

        $this->assertTrue($parentCompanyAdmin->canManageUser($multiCompanyUser->fresh()));
        $this->assertFalse($clientCompanyAdmin->canManageUser($multiCompanyUser->fresh()));
        $this->actingAs($clientCompanyAdmin)->get('/user/update/'.$multiCompanyUser->id)->assertNotFound();
    }

    #[Test]
    public function the_list_shows_the_default_company_and_the_extra_companies_of_each_user(): void
    {
        $this->seed([RoleSeeder::class, PermissionSeeder::class]);
        $parentCompany = Company::factory()->create(['name' => 'Parent company']);
        $firstClientCompany = Company::factory()->create(['name' => 'Z Client company']);
        $secondClientCompany = Company::factory()->create(['name' => 'A Client company']);
        $multiCompanyUser = User::factory()->create(['company_id' => $parentCompany->id]);
        app(CompanyAccessService::class)->grant($multiCompanyUser, $firstClientCompany);
        app(CompanyAccessService::class)->grant($multiCompanyUser->fresh(), $secondClientCompany);
        $multiCompanyUser->refresh();
        $multiCompanyUser->company_id = $firstClientCompany->id;
        $multiCompanyUser->save();
        $parentCompanyAdmin = User::factory()->create(['company_id' => $parentCompany->id]);
        $parentCompanyAdmin->assignRole('CompanyAdmin');
        $expectedExtraCompanies = __('Also has access to: :companies', ['companies' => 'A Client company, Z Client company']);

        $this->get('/user')
            ->assertOk()
            ->assertSee($expectedExtraCompanies);
        $this->actingAs($parentCompanyAdmin)->get('/user')
            ->assertOk()
            ->assertSeeInOrder([$multiCompanyUser->email, 'Parent company', $expectedExtraCompanies]);
    }

    #[Test]
    public function users_without_company_admin_role_do_not_see_the_company_column(): void
    {
        $this->seed([RoleSeeder::class, PermissionSeeder::class]);
        $user = User::factory()->create();
        $user->givePermissionTo('read user');

        $this->actingAs($user)->get('/user')
            ->assertOk()
            ->assertDontSee('?sort=company', false);
    }
}
