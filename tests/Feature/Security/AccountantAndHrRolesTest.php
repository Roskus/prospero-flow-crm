<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Access of the Accountant and HR roles, using the real role/permission matrix from PermissionSeeder.
 */
class AccountantAndHrRolesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([RoleSeeder::class, PermissionSeeder::class]);
    }

    /**
     * @return array<string, array{string, string, int}>
     */
    public static function accessMatrix(): array
    {
        return [
            'accountant accounting' => ['Accountant', '/accounting', 200],
            'accountant sales report' => ['Accountant', '/report/sale', 200],
            'accountant orders' => ['Accountant', '/order', 200],
            'accountant customers' => ['Accountant', '/customer', 200],
            'accountant suppliers' => ['Accountant', '/supplier', 200],
            'accountant payroll' => ['Accountant', '/payroll', 200],
            'accountant new payroll' => ['Accountant', '/payroll/create', 200],
            'accountant users' => ['Accountant', '/user', 403],
            'accountant hr' => ['Accountant', '/rrhh', 403],
            'accountant new order' => ['Accountant', '/order/create', 403],
            'hr users' => ['HR', '/user', 200],
            'hr new user' => ['HR', '/user/create', 200],
            'hr hr' => ['HR', '/rrhh', 200],
            'hr approvals' => ['HR', '/rrhh/approvals', 200],
            'hr new employee' => ['HR', '/rrhh/employee/create', 200],
            'hr payroll' => ['HR', '/payroll', 200],
            'hr new payroll' => ['HR', '/payroll/create', 200],
            'hr reports menu' => ['HR', '/report', 200],
            'hr sales report' => ['HR', '/report/sale', 403],
            'hr sales report pdf' => ['HR', '/report/sale/pdf', 403],
            'hr accounting' => ['HR', '/accounting', 403],
            'hr customers' => ['HR', '/customer', 403],
        ];
    }

    #[Test]
    #[DataProvider('accessMatrix')]
    public function role_has_the_agreed_access(string $role, string $uri, int $status): void
    {
        $this->actingAs($this->createUserWithRole($role))->get($uri)->assertStatus($status);
    }

    #[Test]
    public function hr_reports_menu_hides_the_sales_report(): void
    {
        $response = $this->actingAs($this->createUserWithRole('HR'))->get('/report');

        $response->assertOk();
        $response->assertDontSee(url('/report/sale'));
        $response->assertSee(url('/report/email'));
    }

    #[Test]
    public function hr_cannot_delete_users(): void
    {
        $hr = $this->createUserWithRole('HR');
        $employee = User::factory()->create(['company_id' => $hr->company_id]);

        $this->actingAs($hr)->delete('/user/delete/'.$employee->id)->assertForbidden();
        $this->assertNotSoftDeleted('user', ['id' => $employee->id]);
    }

    #[Test]
    public function hr_creates_a_user_in_its_own_company_with_a_non_admin_role(): void
    {
        $hr = $this->createUserWithRole('HR');

        $response = $this->actingAs($hr)->post('/user/save', $this->userPayload(['email' => 'new.seller@example.com', 'roles' => ['Seller']]));

        $response->assertRedirect('/user');
        $user = User::where('email', 'new.seller@example.com')->firstOrFail();
        $this->assertSame((int) $hr->company_id, (int) $user->company_id);
        $this->assertTrue($user->hasRole('Seller'));
    }

    #[Test]
    public function hr_cannot_assign_administrator_roles(): void
    {
        $hr = $this->createUserWithRole('HR');

        foreach (User::ADMINISTRATOR_ROLES as $role) {
            $response = $this->actingAs($hr)->from('/user/create')
                ->post('/user/save', $this->userPayload(['email' => strtolower($role).'@example.com', 'roles' => [$role]]));

            $response->assertSessionHasErrors(['roles.0' => __('You are not allowed to assign this role.')]);
            $this->assertDatabaseMissing('user', ['email' => strtolower($role).'@example.com']);
        }
    }

    #[Test]
    public function hr_role_selector_only_offers_non_admin_roles(): void
    {
        $response = $this->actingAs($this->createUserWithRole('HR'))->get('/user/create');

        $response->assertOk();
        $response->assertSee('value="Seller"', false);
        $response->assertSee('value="Accountant"', false);
        $response->assertDontSee('value="CompanyAdmin"', false);
        $response->assertDontSee('value="SuperAdmin"', false);
    }

    #[Test]
    public function hr_cannot_open_or_edit_a_company_admin(): void
    {
        $hr = $this->createUserWithRole('HR');
        $companyAdmin = $this->createUserWithRole('CompanyAdmin', $hr->company_id);

        $this->actingAs($hr)->get('/user/update/'.$companyAdmin->id)->assertForbidden();
        $this->actingAs($hr)->post('/user/save', $this->userPayload([
            'id' => $companyAdmin->id,
            'email' => $companyAdmin->email,
            'password' => 'hijacked-password',
            'password_confirmation' => 'hijacked-password',
        ]))->assertForbidden();

        $this->assertSame($companyAdmin->password, $companyAdmin->fresh()->password);
    }

    #[Test]
    public function hr_can_edit_a_regular_user_of_its_company(): void
    {
        $hr = $this->createUserWithRole('HR');
        $employee = $this->createUserWithRole('Seller', $hr->company_id);

        $response = $this->actingAs($hr)->post('/user/save', $this->userPayload([
            'id' => $employee->id,
            'first_name' => 'Renamed',
            'email' => $employee->email,
            'roles' => ['Accountant'],
        ]));

        $response->assertRedirect('/user');
        $this->assertSame('Renamed', $employee->fresh()->first_name);
        $this->assertTrue($employee->fresh()->hasRole('Accountant'));
    }

    #[Test]
    public function hr_cannot_edit_users_of_another_company(): void
    {
        $hr = $this->createUserWithRole('HR');
        $foreignUser = $this->createUserWithRole('Seller');

        $this->actingAs($hr)->get('/user/update/'.$foreignUser->id)->assertNotFound();
        $this->actingAs($hr)->post('/user/save', $this->userPayload(['id' => $foreignUser->id, 'email' => $foreignUser->email]))
            ->assertForbidden();
    }

    #[Test]
    public function accountant_and_hr_see_ibans_and_support_does_not(): void
    {
        $this->assertTrue($this->createUserWithRole('Accountant')->canSeeBankDetails());
        $this->assertTrue($this->createUserWithRole('HR')->canSeeBankDetails());
        $this->assertFalse($this->createUserWithRole('Support')->canSeeBankDetails());
    }

    #[Test]
    public function both_roles_keep_employee_self_service(): void
    {
        foreach (['Accountant', 'HR'] as $role) {
            $user = $this->createUserWithRole($role);

            $this->assertTrue($user->can('read own payroll'), $role);
            $this->assertTrue($user->can('manage own timeoff'), $role);
            $this->assertTrue($user->can('manage own timesheet'), $role);
        }
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function userPayload(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane.doe@example.com',
            'lang' => 'es',
            'timezone' => 'Europe/Madrid',
            'password' => 'initial-password',
            'password_confirmation' => 'initial-password',
        ], $overrides);
    }

    private function createUserWithRole(string $role, ?int $companyId = null): User
    {
        $user = User::factory()->create($companyId ? ['company_id' => $companyId] : []);
        $user->assignRole($role);

        return $user;
    }
}
