<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\User;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserDeleteControllerTest extends TestCase
{
    #[Test]
    public function super_admin_can_delete_a_user_of_another_company(): void
    {
        $otherCompanyUser = User::factory()->create();

        $response = $this->delete('/user/delete/'.$otherCompanyUser->id);

        $response->assertRedirect('/user');
        $this->assertSoftDeleted('user', ['id' => $otherCompanyUser->id]);
    }

    #[Test]
    public function it_returns_not_found_for_a_missing_user(): void
    {
        $response = $this->delete('/user/delete/999999');

        $response->assertNotFound();
    }

    #[Test]
    public function company_admin_can_delete_a_user_of_the_same_company(): void
    {
        $companyAdmin = $this->createCompanyAdmin();
        $victim = User::factory()->create(['company_id' => $companyAdmin->company_id]);

        $response = $this->actingAs($companyAdmin)->delete('/user/delete/'.$victim->id);

        $response->assertRedirect('/user');
        $this->assertSoftDeleted('user', ['id' => $victim->id]);
    }

    #[Test]
    public function company_admin_cannot_delete_a_user_of_another_company(): void
    {
        $companyAdmin = $this->createCompanyAdmin();
        $victim = User::factory()->create();

        $response = $this->actingAs($companyAdmin)->delete('/user/delete/'.$victim->id);

        $response->assertNotFound();
        $this->assertNotSoftDeleted('user', ['id' => $victim->id]);
    }

    #[Test]
    public function company_admin_cannot_delete_a_super_admin_of_the_same_company(): void
    {
        $companyAdmin = $this->createCompanyAdmin();
        $superAdmin = User::factory()->create(['company_id' => $companyAdmin->company_id]);
        $superAdmin->assignRole('SuperAdmin');

        $response = $this->actingAs($companyAdmin)->delete('/user/delete/'.$superAdmin->id);

        $response->assertForbidden();
        $this->assertNotSoftDeleted('user', ['id' => $superAdmin->id]);
    }

    #[Test]
    public function seller_cannot_delete_a_user_of_the_same_company(): void
    {
        $seller = User::factory()->create();
        $seller->assignRole(Role::findOrCreate('Seller', 'web'));
        $victim = User::factory()->create(['company_id' => $seller->company_id]);

        $response = $this->actingAs($seller)->delete('/user/delete/'.$victim->id);

        $response->assertForbidden();
        $this->assertNotSoftDeleted('user', ['id' => $victim->id]);
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
