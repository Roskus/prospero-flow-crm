<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * IBANs are payroll data: only roles with 'read payroll' (SuperAdmin, CompanyAdmin) may see other users' IBAN.
 */
class UserBankDetailsVisibilityTest extends TestCase
{
    private const string IBAN = 'ES9121000418450200051332';

    private User $employee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([RoleSeeder::class, PermissionSeeder::class]);

        $this->employee = User::factory()->create(['iban' => self::IBAN]);
    }

    #[Test]
    public function support_does_not_get_ibans_from_the_users_api(): void
    {
        $support = $this->createUserWithRole('Support');

        $list = $this->actingAs($support, 'api')->getJson('/api/user');
        $read = $this->actingAs($support, 'api')->getJson('/api/user/'.$this->employee->id);

        $list->assertOk()->assertDontSee(self::IBAN);
        $this->assertArrayNotHasKey('iban', collect($list->json('users'))->firstWhere('id', $this->employee->id));
        $read->assertOk()->assertDontSee(self::IBAN);
        $this->assertArrayNotHasKey('iban', $read->json('user'));
    }

    #[Test]
    public function company_admin_gets_ibans_from_the_users_api(): void
    {
        $companyAdmin = $this->createUserWithRole('CompanyAdmin');

        $this->actingAs($companyAdmin, 'api')->getJson('/api/user')->assertOk()->assertSee(self::IBAN);
        $this->actingAs($companyAdmin, 'api')->getJson('/api/user/'.$this->employee->id)
            ->assertOk()
            ->assertJsonPath('user.iban', self::IBAN);
    }

    #[Test]
    public function support_does_not_see_the_iban_in_the_user_form(): void
    {
        $support = $this->createUserWithRole('Support');

        $response = $this->actingAs($support)->get('/user/update/'.$this->employee->id);

        $response->assertOk();
        $response->assertDontSee('name="iban"', false);
        $response->assertDontSee(self::IBAN);
    }

    #[Test]
    public function company_admin_sees_the_iban_in_the_user_form(): void
    {
        $companyAdmin = $this->createUserWithRole('CompanyAdmin');

        $response = $this->actingAs($companyAdmin)->get('/user/update/'.$this->employee->id);

        $response->assertOk();
        $response->assertSee('name="iban"', false);
        $response->assertSee(self::IBAN);
    }

    #[Test]
    public function saving_a_user_without_the_iban_field_keeps_the_stored_iban(): void
    {
        $response = $this->post('/user/save', [
            'id' => $this->employee->id,
            'first_name' => 'Updated',
            'last_name' => $this->employee->last_name,
            'email' => $this->employee->email,
            'lang' => 'es',
            'company_id' => $this->employee->company_id,
        ]);

        $response->assertRedirect('/user');
        $this->assertSame(self::IBAN, $this->employee->fresh()->iban);
    }

    #[Test]
    public function auth_me_and_logout_require_a_token(): void
    {
        $this->app['auth']->forgetGuards();

        $this->postJson('/api/auth/me')->assertUnauthorized();
        $this->postJson('/api/auth/logout')->assertUnauthorized();
    }

    private function createUserWithRole(string $role): User
    {
        $user = User::factory()->create(['company_id' => $this->employee->company_id]);
        $user->assignRole($role);

        return $user;
    }
}
