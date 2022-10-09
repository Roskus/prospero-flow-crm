<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

/**
 * php artisan db:seed --class=PermissionSeeder
 */
class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $superAdmin = Role::findByName('SuperAdmin');
        $companyAdmin = Role::findByName('CompanyAdmin');
        $seller = Role::findByName('Seller');

        //Order
        $createOrder = Permission::create(['name' => 'create order']);
        $readOrder = Permission::create(['name' => 'read order']);
        $updateOrder = Permission::create(['name' => 'update order']);
        $deleteOrder = Permission::create(['name' => 'delete order']);

        $superAdmin->givePermissionTo($createOrder);
        $superAdmin->givePermissionTo($readOrder);
        $superAdmin->givePermissionTo($updateOrder);
        $superAdmin->givePermissionTo($deleteOrder);

        $companyAdmin->givePermissionTo($createOrder);
        $companyAdmin->givePermissionTo($readOrder);
        $companyAdmin->givePermissionTo($updateOrder);
        $companyAdmin->givePermissionTo($deleteOrder);

        //Company
        $createCompany = Permission::create(['name' => 'create company']);
        $readCompany = Permission::create(['name' => 'read company']);
        $updateCompany = Permission::create(['name' => 'update company']);
        $deleteCompany = Permission::create(['name' => 'delete company']);

        $superAdmin->givePermissionTo($createCompany);
        $superAdmin->givePermissionTo($readCompany);
        $superAdmin->givePermissionTo($updateCompany);
        $superAdmin->givePermissionTo($deleteCompany);

        //User
        $createUser = Permission::create(['name' => 'create user']);
        $readUser = Permission::create(['name' => 'read user']);
        $updateUpdate = Permission::create(['name' => 'update user']);
        $deleteUser = Permission::create(['name' => 'delete user']);

        $superAdmin->givePermissionTo($createUser);
        $superAdmin->givePermissionTo($readUser);
        $superAdmin->givePermissionTo($updateUpdate);
        $superAdmin->givePermissionTo($deleteUser);

        //Company Admin can't create user by default
        $companyAdmin->givePermissionTo($readUser);
        $companyAdmin->givePermissionTo($updateUpdate);
        $companyAdmin->givePermissionTo($deleteUser);
    }
}
