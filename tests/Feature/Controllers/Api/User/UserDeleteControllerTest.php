<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\User;

use App\Models\Company;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserDeleteControllerTest extends TestCase
{
    #[Test]
    public function it_blocks_a_seller_from_deleting_a_user_of_the_same_company(): void
    {
        $seller = User::factory()->create();
        $seller->assignRole(Role::findOrCreate('Seller', 'web'));

        $victim = User::factory()->create(['company_id' => $seller->company_id]);

        $response = $this->actingAs($seller, 'api')
            ->deleteJson("/api/user/{$victim->id}");

        $response->assertForbidden();
        $this->assertNotSoftDeleted('user', ['id' => $victim->id]);
    }

    #[Test]
    public function it_blocks_a_default_company_seller_from_deleting_a_user_of_another_company(): void
    {
        $defaultCompany = Company::find(Company::DEFAULT_COMPANY) ?? Company::factory()->create(['id' => Company::DEFAULT_COMPANY]);

        $seller = User::factory()->create(['company_id' => $defaultCompany->id]);
        $seller->assignRole(Role::findOrCreate('Seller', 'web'));

        $victim = User::factory()->create();

        $response = $this->actingAs($seller, 'api')
            ->deleteJson("/api/user/{$victim->id}");

        $response->assertForbidden();
        $this->assertNotSoftDeleted('user', ['id' => $victim->id]);
    }

    #[Test]
    public function super_admin_can_delete_a_user_of_another_company(): void
    {
        $otherCompanyUser = User::factory()->create();

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson("/api/user/{$otherCompanyUser->id}");

        $response->assertOk();
        $response->assertJsonFragment(['message' => 'User deleted successfully']);
        $this->assertSoftDeleted('user', ['id' => $otherCompanyUser->id]);
    }

    #[Test]
    public function it_returns_not_found_for_a_missing_user(): void
    {
        $response = $this->actingAs($this->user, 'api')
            ->deleteJson('/api/user/999999');

        $response->assertNotFound();
    }

    #[Test]
    public function it_rejects_unauthenticated_requests(): void
    {
        $victim = User::factory()->create();

        $this->app['auth']->forgetGuards();

        $response = $this->deleteJson("/api/user/{$victim->id}");

        $response->assertUnauthorized();
        $this->assertNotSoftDeleted('user', ['id' => $victim->id]);
    }

    #[Test]
    public function company_admin_can_delete_a_user_of_the_same_company(): void
    {
        $companyAdmin = $this->createCompanyAdmin();
        $victim = User::factory()->create(['company_id' => $companyAdmin->company_id]);

        $response = $this->actingAs($companyAdmin, 'api')
            ->deleteJson("/api/user/{$victim->id}");

        $response->assertOk();
        $this->assertSoftDeleted('user', ['id' => $victim->id]);
    }

    #[Test]
    public function company_admin_cannot_delete_a_user_of_another_company(): void
    {
        $companyAdmin = $this->createCompanyAdmin();
        $victim = User::factory()->create();

        $response = $this->actingAs($companyAdmin, 'api')
            ->deleteJson("/api/user/{$victim->id}");

        $response->assertNotFound();
        $this->assertNotSoftDeleted('user', ['id' => $victim->id]);
    }

    #[Test]
    public function company_admin_cannot_delete_a_super_admin_of_the_same_company(): void
    {
        $companyAdmin = $this->createCompanyAdmin();
        $superAdmin = User::factory()->create(['company_id' => $companyAdmin->company_id]);
        $superAdmin->assignRole('SuperAdmin');

        $response = $this->actingAs($companyAdmin, 'api')
            ->deleteJson("/api/user/{$superAdmin->id}");

        $response->assertForbidden();
        $this->assertNotSoftDeleted('user', ['id' => $superAdmin->id]);
    }

    private function createCompanyAdmin(): User
    {
        Permission::findOrCreate('delete user', 'web');
        $role = Role::findOrCreate('CompanyAdmin', 'web');
        $role->givePermissionTo('delete user');

        $companyAdmin = User::factory()->create();
        $companyAdmin->assignRole($role);

        return $companyAdmin;
    }
}
