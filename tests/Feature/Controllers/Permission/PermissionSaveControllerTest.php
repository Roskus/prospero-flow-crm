<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Permission;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionSaveControllerTest extends TestCase
{
    #[Test]
    public function it_requires_authentication_to_view_permissions(): void
    {
        $response = $this->get('/permission');

        $response->assertOk();
    }

    #[Test]
    public function it_blocks_unauthenticated_access_to_view_permissions(): void
    {
        $this->post('/logout');

        $response = $this->get('/permission');

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_blocks_unauthenticated_permission_save(): void
    {
        $this->post('/logout');

        $response = $this->post('/permission', []);

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_can_save_permissions(): void
    {
        $role = Role::create(['name' => 'Reviewer', 'guard_name' => 'web']);
        $permission = Permission::create(['name' => 'edit articles', 'guard_name' => 'web']);

        $response = $this->post('/permission', [
            'roles' => [$role->id => [$permission->id => 1]],
        ]);

        $response->assertRedirect('/permission');
        $this->assertTrue($role->fresh()->hasPermissionTo('edit articles'));
    }

    #[Test]
    public function it_groups_permissions_by_object_and_lists_actions_below(): void
    {
        Permission::create(['name' => 'create customer', 'guard_name' => 'web']);
        Permission::create(['name' => 'read customer', 'guard_name' => 'web']);
        Permission::create(['name' => 'update customer', 'guard_name' => 'web']);

        $response = $this->get('/permission');

        $response->assertOk();
        $response->assertSeeInOrder([
            'Customer',
            'Create',
            'Read',
            'Update',
        ]);
    }

    #[Test]
    public function it_shows_the_buyer_role_column_in_permissions_index(): void
    {
        Role::findOrCreate('Buyer', 'web');

        $response = $this->get('/permission');

        $response->assertOk();
        $response->assertSeeText('Buyer');
    }

    #[Test]
    public function it_allows_company_admin_to_view_permissions(): void
    {
        $companyAdminRole = Role::findOrCreate('CompanyAdmin', 'web');
        $companyAdmin = User::factory()->create();
        $companyAdmin->assignRole($companyAdminRole);

        $response = $this->actingAs($companyAdmin)->get('/permission');

        $response->assertOk();
    }

    #[Test]
    public function it_blocks_seller_from_viewing_permissions(): void
    {
        $sellerRole = Role::findOrCreate('Seller', 'web');
        $seller = User::factory()->create();
        $seller->assignRole($sellerRole);

        $response = $this->actingAs($seller)->get('/permission');

        $response->assertStatus(403);
    }

    #[Test]
    public function it_allows_company_admin_to_save_permissions(): void
    {
        $companyAdminRole = Role::findOrCreate('CompanyAdmin', 'web');
        $companyAdmin = User::factory()->create();
        $companyAdmin->assignRole($companyAdminRole);

        $role = Role::create(['name' => 'ReviewerCompany', 'guard_name' => 'web']);
        $permission = Permission::create(['name' => 'edit articles company', 'guard_name' => 'web']);

        $response = $this->actingAs($companyAdmin)->post('/permission', [
            'roles' => [$role->id => [$permission->id => $permission->id]],
        ]);

        $response->assertRedirect('/permission');
        $this->assertTrue($role->fresh()->hasPermissionTo('edit articles company'));
    }

    #[Test]
    public function it_blocks_company_admin_from_modifying_super_admin_role(): void
    {
        $companyAdminRole = Role::findOrCreate('CompanyAdmin', 'web');
        $companyAdmin = User::factory()->create();
        $companyAdmin->assignRole($companyAdminRole);

        $superAdminRole = Role::findOrCreate('SuperAdmin', 'web');
        $permission = Permission::create(['name' => 'super admin only permission', 'guard_name' => 'web']);

        $response = $this->actingAs($companyAdmin)->post('/permission', [
            'roles' => [$superAdminRole->id => [$permission->id => $permission->id]],
        ]);

        $response->assertStatus(403);
        $this->assertFalse($superAdminRole->fresh()->hasPermissionTo('super admin only permission'));
    }

    #[Test]
    public function it_allows_company_admin_to_modify_non_super_admin_role(): void
    {
        $companyAdminRole = Role::findOrCreate('CompanyAdmin', 'web');
        $companyAdmin = User::factory()->create();
        $companyAdmin->assignRole($companyAdminRole);

        $sellerRole = Role::findOrCreate('Seller', 'web');
        $permission = Permission::create(['name' => 'seller permission', 'guard_name' => 'web']);

        $response = $this->actingAs($companyAdmin)->post('/permission', [
            'roles' => [$sellerRole->id => [$permission->id => $permission->id]],
        ]);

        $response->assertRedirect('/permission');
        $this->assertTrue($sellerRole->fresh()->hasPermissionTo('seller permission'));
    }

    #[Test]
    public function it_blocks_seller_from_saving_permissions(): void
    {
        $sellerRole = Role::findOrCreate('Seller', 'web');
        $seller = User::factory()->create();
        $seller->assignRole($sellerRole);

        $role = Role::create(['name' => 'ReviewerSeller', 'guard_name' => 'web']);
        $permission = Permission::create(['name' => 'edit articles seller', 'guard_name' => 'web']);

        $response = $this->actingAs($seller)->post('/permission', [
            'roles' => [$role->id => [$permission->id => $permission->id]],
        ]);

        $response->assertStatus(403);
        $this->assertFalse($role->fresh()->hasPermissionTo('edit articles seller'));
    }
}
