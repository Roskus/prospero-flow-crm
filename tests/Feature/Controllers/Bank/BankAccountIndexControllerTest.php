<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Bank;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BankAccountIndexControllerTest extends TestCase
{
    /**
     * @return array<string, array{string}>
     */
    public static function rolesWithAccountingAccess(): array
    {
        return [
            'company admin' => ['CompanyAdmin'],
            'multi-company admin' => [User::MULTI_COMPANY_ADMIN],
            'accountant' => ['Accountant'],
        ];
    }

    #[Test]
    #[DataProvider('rolesWithAccountingAccess')]
    public function roles_with_accounting_access_can_list_bank_accounts(string $role): void
    {
        $this->seed([RoleSeeder::class, PermissionSeeder::class]);
        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/bank-account')->assertOk();
    }

    #[Test]
    public function users_without_accounting_access_cannot_list_bank_accounts(): void
    {
        $this->actingAs(User::factory()->create())->get('/bank-account')->assertForbidden();
    }
}
