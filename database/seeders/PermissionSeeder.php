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
            $guardName = $permission['guard_name'];
            $moduleId = $permission['module_id'] ?? null;
            $permissionObject = null;

            try {
                $permissionObject = Permission::firstOrCreate(
                    [
                        'name' => $name,
                        'guard_name' => $guardName,
                        'module_id' => $moduleId,
                    ]
                );
            } catch (Throwable $throwable) {
                $permissionObject = Permission::findByName($name, $guardName);
                $permissionObject->module_id = $moduleId;
                $permissionObject->updated_at = now();
                $permissionObject->save();
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
            // Dashboard
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller', 'Support'],
                'name' => 'read dashboard',
                'guard_name' => 'web',
                'module_id' => null,
            ],
            // Lead
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller'],
                'name' => 'create lead',
                'guard_name' => 'web',
                'module_id' => 1,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller'],
                'name' => 'read lead',
                'guard_name' => 'web',
                'module_id' => 1,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller'],
                'name' => 'update lead',
                'guard_name' => 'web',
                'module_id' => 1,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'delete lead',
                'guard_name' => 'web',
                'module_id' => 1,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'export lead',
                'guard_name' => 'web',
                'module_id' => 1,
            ],
            // Customer
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller', 'Support'],
                'name' => 'create customer',
                'guard_name' => 'web',
                'module_id' => 2,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller', 'Support'],
                'name' => 'read customer',
                'guard_name' => 'web',
                'module_id' => 2,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller', 'Support'],
                'name' => 'update customer',
                'guard_name' => 'web',
                'module_id' => 2,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'delete customer',
                'guard_name' => 'web',
                'module_id' => 2,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'export customer',
                'guard_name' => 'web',
                'module_id' => 2,
            ],
            // Product
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller'],
                'name' => 'create product',
                'guard_name' => 'web',
                'module_id' => 3,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller', 'Support'],
                'name' => 'read product',
                'guard_name' => 'web',
                'module_id' => 3,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller'],
                'name' => 'update product',
                'guard_name' => 'web',
                'module_id' => 3,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'delete product',
                'guard_name' => 'web',
                'module_id' => 3,
            ],
            // Order
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller', 'Support'],
                'name' => 'create order',
                'guard_name' => 'web',
                'module_id' => 4,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller', 'Support'],
                'name' => 'read order',
                'guard_name' => 'web',
                'module_id' => 4,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller', 'Support'],
                'name' => 'update order',
                'guard_name' => 'web',
                'module_id' => 4,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'delete order',
                'guard_name' => 'web',
                'module_id' => 4,
            ],
            // Company
            [
                'roles' => ['SuperAdmin'], // Only SuperAdmin can create company
                'name' => 'create company',
                'guard_name' => 'web',
                'module_id' => 8,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Support'],
                'name' => 'read company',
                'guard_name' => 'web',
                'module_id' => 8,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'update company',
                'guard_name' => 'web',
                'module_id' => 8,
            ],
            [
                'roles' => ['SuperAdmin'], // Only SuperAdmin can delete company
                'name' => 'delete company',
                'guard_name' => 'web',
                'module_id' => 8,
            ],
            // User
            [
                'roles' => ['SuperAdmin'], // Only SuperAdmin can create user
                'name' => 'create user',
                'guard_name' => 'web',
                'module_id' => 7,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Support'],
                'name' => 'read user',
                'guard_name' => 'web',
                'module_id' => 7,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Support'],
                'name' => 'update user',
                'guard_name' => 'web',
                'module_id' => 7,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Support'],
                'name' => 'delete user',
                'guard_name' => 'web',
                'module_id' => 7,
            ],
            // Supplier
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'create supplier',
                'guard_name' => 'web',
                'module_id' => 5,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Support'],
                'name' => 'read supplier',
                'guard_name' => 'web',
                'module_id' => 5,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'update supplier',
                'guard_name' => 'web',
                'module_id' => 5,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'delete supplier',
                'guard_name' => 'web',
                'module_id' => 5,
            ],
            // Accounting
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'create accounting',
                'guard_name' => 'web',
                'module_id' => 6,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'read accounting',
                'guard_name' => 'web',
                'module_id' => 6,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'update accounting',
                'guard_name' => 'web',
                'module_id' => 6,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'delete accounting',
                'guard_name' => 'web',
                'module_id' => 6,
            ],
            // Bank card
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'bank card view number',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin'],
                'name' => 'bank card view cvv',
                'guard_name' => 'web',
            ],
            // Report
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller', 'Support'],
                'name' => 'read report',
                'guard_name' => 'web',
                'module_id' => 9,
            ],
            // Message
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'delete message',
                'guard_name' => 'web',
            ],
            // RRHH
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'read rrhh',
                'guard_name' => 'web',
                'module_id' => 14,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'create rrhh',
                'guard_name' => 'web',
                'module_id' => 14,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'update rrhh',
                'guard_name' => 'web',
                'module_id' => 14,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'delete rrhh',
                'guard_name' => 'web',
                'module_id' => 14,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'approve timeoff',
                'guard_name' => 'web',
                'module_id' => 14,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'manage schedule',
                'guard_name' => 'web',
                'module_id' => 14,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'manage holidays',
                'guard_name' => 'web',
                'module_id' => 14,
            ],
            // Ticket
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Support'],
                'name' => 'create ticket',
                'guard_name' => 'web',
                'module_id' => 10,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Support'],
                'name' => 'read ticket',
                'guard_name' => 'web',
                'module_id' => 10,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Support'],
                'name' => 'update ticket',
                'guard_name' => 'web',
                'module_id' => 10,
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'delete ticket',
                'guard_name' => 'web',
                'module_id' => 10,
            ],
            // Brand
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller'],
                'name' => 'create brand',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller', 'Support'],
                'name' => 'read brand',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller'],
                'name' => 'update brand',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'delete brand',
                'guard_name' => 'web',
            ],
            // Category
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller'],
                'name' => 'create category',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller', 'Support'],
                'name' => 'read category',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller'],
                'name' => 'update category',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'delete category',
                'guard_name' => 'web',
            ],
            // Calendar
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller', 'Support'],
                'name' => 'create calendar',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller', 'Support'],
                'name' => 'read calendar',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller', 'Support'],
                'name' => 'update calendar',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'delete calendar',
                'guard_name' => 'web',
            ],
            // Campaign
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller'],
                'name' => 'create campaign',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller', 'Support'],
                'name' => 'read campaign',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller'],
                'name' => 'update campaign',
                'guard_name' => 'web',
            ],
            // Contact
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller', 'Support'],
                'name' => 'create contact',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller', 'Support'],
                'name' => 'read contact',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller', 'Support'],
                'name' => 'update contact',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'delete contact',
                'guard_name' => 'web',
            ],
            // Currency
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Support'],
                'name' => 'read currency',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'create currency',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'update currency',
                'guard_name' => 'web',
            ],
            // Email
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller', 'Support'],
                'name' => 'create email',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller', 'Support'],
                'name' => 'read email',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin', 'Seller', 'Support'],
                'name' => 'update email',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'delete email',
                'guard_name' => 'web',
            ],
            // Payroll
            // Cada empleado puede ver su propio payroll (controlado por controlador).
            // Ver listado completo y operaciones CRUD:
            //   SuperAdmin, CompanyAdmin (actual)
            //   TODO: agregar rol RRHH cuando se cree
            //   TODO: agregar rol Accountant cuando se cree
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'create payroll',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'read payroll',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'update payroll',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'delete payroll',
                'guard_name' => 'web',
            ],
            // Transaction (route-level permission, complements accounting)
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'create transaction',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'read transaction',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'update transaction',
                'guard_name' => 'web',
            ],
            [
                'roles' => ['SuperAdmin', 'CompanyAdmin'],
                'name' => 'delete transaction',
                'guard_name' => 'web',
            ],
        ];
    }
}
