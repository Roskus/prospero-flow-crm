<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\User;

use App\Models\Company;
use App\Models\User;
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
}
