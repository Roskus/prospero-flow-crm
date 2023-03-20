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

        /*
        $superAdmin = Role::findByName('SuperAdmin');
        $companyAdmin = Role::findByName('CompanyAdmin');
        $seller = Role::findByName('Seller');

        // Prospect
        try {
            $deleteProspect = Permission::create(['name' => 'delete prospect']);
        } catch (Throwable $throwable) {
            $deleteProspect = Permission::findByName('delete prospect');
        }

        try {
            $exportProspect = Permission::create(['name' => 'export prospect']);
        } catch (Throwable $throwable) {
            $deleteProspect = Permission::findByName('export prospect');
        }

        if (!$superAdmin->hasPermissionTo($deleteProspect)) {
            $superAdmin->givePermissionTo($deleteProspect);
        }

        if (!$companyAdmin->hasPermissionTo($deleteProspect)) {
            $companyAdmin->givePermissionTo($deleteProspect);
        }

        if (!$superAdmin->hasPermissionTo($exportProspect)) {
            $superAdmin->givePermissionTo($exportProspect);
        }

        if (!$companyAdmin->hasPermissionTo($exportProspect)) {
            $companyAdmin->givePermissionTo($exportProspect);
        }


        // Customer
        try {
            $deleteCustomer = Permission::create(['name' => 'delete customer']);
        } catch (Throwable $throwable) {
            $deleteCustomer = Permission::findByName('delete customer');
        }

        $exportCustomer = Permission::create(['name' => 'export customer']);

        if (!$superAdmin->hasPermissionTo($deleteCustomer)) {
            $superAdmin->givePermissionTo($deleteCustomer);
        }

        if (!$companyAdmin->hasPermissionTo($deleteCustomer)) {
            $companyAdmin->givePermissionTo($deleteCustomer);
        }

        if (!$superAdmin->hasPermissionTo($exportCustomer)) {
            $superAdmin->givePermissionTo($exportCustomer);
        }

        if (!$companyAdmin->hasPermissionTo($exportCustomer)) {
            $companyAdmin->givePermissionTo($exportCustomer);
        }

        //Order
        $createOrder = Permission::create(['name' => 'create order']);
        $readOrder = Permission::create(['name' => 'read order']);
        $updateOrder = Permission::create(['name' => 'update order']);
        $deleteOrder = Permission::create(['name' => 'delete order']);

        if (!$superAdmin->hasPermissionTo($createOrder)) {
            $superAdmin->givePermissionTo($createOrder);
        }

        if (!$superAdmin->hasPermissionTo($readOrder)) {
            $superAdmin->givePermissionTo($readOrder);
        }

        if (!$superAdmin->hasPermissionTo($updateOrder)) {
            $superAdmin->givePermissionTo($updateOrder);
        }

        if (!$superAdmin->hasPermissionTo($deleteOrder)) {
            $superAdmin->givePermissionTo($deleteOrder);
        }

        if (!$companyAdmin->hasPermissionTo($createOrder)) {
            $companyAdmin->givePermissionTo($createOrder);
        }

        if (!$companyAdmin->hasPermissionTo($readOrder)) {
            $companyAdmin->givePermissionTo($readOrder);
        }

        if (!$companyAdmin->hasPermissionTo($updateOrder)) {
            $companyAdmin->givePermissionTo($updateOrder);
        }

        if (!$companyAdmin->hasPermissionTo($deleteOrder)) {
            $companyAdmin->givePermissionTo($deleteOrder);
        }

        //Company
        $createCompany = Permission::create(['name' => 'create company']);
        $readCompany = Permission::create(['name' => 'read company']);
        $updateCompany = Permission::create(['name' => 'update company']);
        $deleteCompany = Permission::create(['name' => 'delete company']);

        $superAdmin->givePermissionTo($createCompany);
        $superAdmin->givePermissionTo($readCompany);
        $superAdmin->givePermissionTo($updateCompany);
        $superAdmin->givePermissionTo($deleteCompany);

        if (!$companyAdmin->hasPermissionTo($readCompany)) {
            $companyAdmin->givePermissionTo($readCompany);
        }

        if (!$companyAdmin->hasPermissionTo($updateCompany)) {
            $companyAdmin->givePermissionTo($updateCompany);
        }
        //User
        $createUser = Permission::create(['name' => 'create user']);
        $readUser = Permission::create(['name' => 'read user']);
        $updateUpdate = Permission::create(['name' => 'update user']);
        $deleteUser = Permission::create(['name' => 'delete user']);

        if (!$superAdmin->hasPermissionTo($createUser)) {
            $superAdmin->givePermissionTo($createUser);
        }

        if (!$superAdmin->hasPermissionTo($readUser)) {
            $superAdmin->givePermissionTo($readUser);
        }

        if (!$superAdmin->hasPermissionTo($updateUpdate)) {
            $superAdmin->givePermissionTo($updateUpdate);
        }

        if (!$superAdmin->hasPermissionTo($deleteUser)) {
            $superAdmin->givePermissionTo($deleteUser);
        }

        //Company Admin can't create user by default
        if (!$companyAdmin->hasPermissionTo($readUser)) {
            $companyAdmin->givePermissionTo($readUser);
        }

        if (!$companyAdmin->hasPermissionTo($updateUpdate)) {
            $companyAdmin->givePermissionTo($updateUpdate);
        }

        if (!$companyAdmin->hasPermissionTo($deleteUser)) {
            $companyAdmin->givePermissionTo($deleteUser);
        }*/

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
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'delete prospect',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'export prospect',
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
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'create order',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'read order',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'update order',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'delete order',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin'], //Only SuperAdmin can create company
                'name' => 'create company',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
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
            [
                'roles' => ['SuperAdmin'], //Only SuperAdmin can create user
                'name' => 'create user',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'read user',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'update user',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
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
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
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
        ];
    }
}
