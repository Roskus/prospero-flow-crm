<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Throwable;

/**
 * Cache clear
 * -----------
 * php artisan cache:clear
 * php artisan cache:forget spatie.permission.cache
 *
 * Re-Generate Permission
 * ----------------------
 * php artisan db:seed --class=PermissionSeeder
 */
class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach ($this->permissions() as $permission) {
            $roles = $permission['roles'];
            $name = $permission['name'];
            $guard_name = $permission['guard_name'];
            $permissionObject = null;

            try {
                $permissionObject = Permission::create([
                    'name' => $name,
                    'guard_name' => $guard_name,
                ]);
            } catch (Throwable $throwable) {
                $permissionObject = Permission::findByName($name, $guard_name);
            }

            foreach ($roles as $roleName) {
                $role = Role::findByName($roleName);
                if (! $role->hasPermissionTo($permissionObject)) {
                    $role->givePermissionTo($permissionObject);
                }
            }
        }
    }

    /**
     * @TODO move this to a config file
     * @return array[]
     */
    public function permissions(): array
    {
        return [
            //Dashboard
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller', 'Support'],
                'name' => 'read dashboard',
                'guard_name' => 'web',
            ],
            // Lead / Customer -> Support can't read user notes
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller'],
                'name' => 'create note',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller'],
                'name' => 'read note',
                'guard_name' => 'web',
            ],
            //Lead
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller'],
                'name' => 'create lead',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller'],
                'name' => 'read lead',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller'],
                'name' => 'update lead',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'delete lead',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'export lead',
                'guard_name' => 'web',
            ],
            //Customer
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller'],
                'name' => 'create customer',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller', 'Support'],
                'name' => 'read customer',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller', 'Support'],
                'name' => 'update customer',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'delete customer',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'export customer',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller'],
                'name' => 'create order',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller', 'Support'],
                'name' => 'read order',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller'],
                'name' => 'update order',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'delete order',
                'guard_name' => 'web',
            ],
            //Company
            [
                'roles' => ['SuperAdmin'], //Only SuperAdmin can create company
                'name' => 'create company',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Support'],
                'name' => 'read company',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'update company',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin'], //Only SuperAdmin can delete company
                'name' => 'delete company',
                'guard_name' => 'web',
            ],
            //User
            [
                'roles' => ['SuperAdmin'], //Only SuperAdmin can create user
                'name' => 'create user',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Support'],
                'name' => 'read user',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Support'],
                'name' => 'update user',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Support'],
                'name' => 'delete user',
                'guard_name' => 'web',
            ],
            //Supplier
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'create supplier',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Support'],
                'name' => 'read supplier',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'update supplier',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'delete supplier',
                'guard_name' => 'web',
            ],
            // Accounting
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'create accounting',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'read accounting',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'update accounting',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'delete accounting',
                'guard_name' => 'web',
            ],
            // Product
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller'],
                'name' => 'create product',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller', 'Support'],
                'name' => 'read product',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'delete product',
                'guard_name' => 'web',
            ],
            // Report
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller', 'Support'],
                'name' => 'read report',
                'guard_name' => 'web',
            ],
        ];
    }
}
