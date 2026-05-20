<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Permission;

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
        $role = Role::create(['name' => 'Buyer', 'guard_name' => 'web']);
        $permission = Permission::create(['name' => 'edit articles', 'guard_name' => 'web']);

        $response = $this->post('/permission', [
            'roles' => [$role->id => [$permission->id => 1]],
        ]);

        $response->assertRedirect('/permission');
        $this->assertTrue($role->fresh()->hasPermissionTo('edit articles'));
    }
}
