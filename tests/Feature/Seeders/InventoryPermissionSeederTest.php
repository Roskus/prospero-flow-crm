<?php

declare(strict_types=1);

namespace Tests\Feature\Seeders;

use Database\Seeders\ModuleSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InventoryPermissionSeederTest extends TestCase
{
    #[Test]
    public function it_seeds_the_inventory_module_and_permissions_idempotently(): void
    {
        $this->seed([RoleSeeder::class, ModuleSeeder::class, PermissionSeeder::class]);
        $this->seed([ModuleSeeder::class, PermissionSeeder::class]);

        $this->assertDatabaseHas('module', ['id' => ModuleSeeder::INVENTORY_MODULE_ID, 'name' => 'Inventory']);
        $this->assertSame(1, DB::table('module')->where('name', 'Inventory')->count());
        $this->assertSame(10, DB::table('module')->count());

        $assetPermissions = Permission::where('name', 'like', '% asset')->get();
        $this->assertEqualsCanonicalizing(['create asset', 'read asset', 'update asset', 'delete asset'], $assetPermissions->pluck('name')->all());
        $this->assertTrue($assetPermissions->every(fn (Permission $permission): bool => $permission->module_id === ModuleSeeder::INVENTORY_MODULE_ID));

        $this->assertTrue(Role::findByName('CompanyAdmin')->hasAllPermissions(['create asset', 'read asset', 'update asset', 'delete asset']));
        $this->assertFalse(Role::findByName('Seller')->hasPermissionTo('read asset'));
    }
}
