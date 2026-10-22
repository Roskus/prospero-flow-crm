<?php

declare(strict_types=1);

namespace Tests\Feature\Seeders;

use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    #[Test]
    public function a_fresh_install_seeds_roles_with_their_permissions(): void
    {
        $this->seed(DatabaseSeeder::class);

        foreach (['CompanyAdmin', 'Seller', 'Support', 'Accountant', 'HR', 'User'] as $roleName) {
            $this->assertNotEmpty(Role::findByName($roleName)->permissions, $roleName.' has no permissions');
        }

        $this->assertTrue(Role::findByName('Accountant')->hasPermissionTo('read accounting'));
        $this->assertTrue(Role::findByName('HR')->hasPermissionTo('read rrhh'));
        $this->assertFalse(Role::findByName('HR')->hasPermissionTo('read sales report'));
    }

    #[Test]
    public function a_fresh_install_gives_every_company_a_main_branch_and_every_user_and_order_a_branch(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(0, Company::withTrashed()->whereDoesntHave('mainBranch')->count());
        $this->assertSame(0, User::withTrashed()->whereNull('default_branch_id')->count());
        $this->assertSame(0, Order::withTrashed()->whereNull('branch_id')->count());
    }
}
