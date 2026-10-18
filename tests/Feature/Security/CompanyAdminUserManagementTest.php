<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * A CompanyAdmin changes roles of its company's users, including CompanyAdmin, but never assigns or manages SuperAdmin.
 */
class CompanyAdminUserManagementTest extends TestCase
{
    private User $companyAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([RoleSeeder::class, PermissionSeeder::class]);

        $this->companyAdmin = $this->createUserWithRole('CompanyAdmin');
    }

    #[Test]
    public function company_admin_can_change_the_role_of_a_user_of_its_company(): void
    {
        $seller = $this->createUserWithRole('Seller', $this->companyAdmin->company_id);

        $response = $this->actingAs($this->companyAdmin)->post('/user/save', $this->payloadFor($seller, ['roles' => ['Accountant']]));

        $response->assertRedirect('/user');
        $this->assertTrue($seller->fresh()->hasRole('Accountant'));
        $this->assertFalse($seller->fresh()->hasRole('Seller'));
    }

    #[Test]
    public function company_admin_can_promote_a_user_to_company_admin(): void
    {
        $seller = $this->createUserWithRole('Seller', $this->companyAdmin->company_id);

        $this->actingAs($this->companyAdmin)->post('/user/save', $this->payloadFor($seller, ['roles' => ['CompanyAdmin']]))
            ->assertRedirect('/user');

        $this->assertTrue($seller->fresh()->hasRole('CompanyAdmin'));
    }

    #[Test]
    public function company_admin_cannot_assign_super_admin_to_others_or_itself(): void
    {
        $seller = $this->createUserWithRole('Seller', $this->companyAdmin->company_id);

        foreach ([$seller, $this->companyAdmin] as $target) {
            $response = $this->actingAs($this->companyAdmin)->from('/user')
                ->post('/user/save', $this->payloadFor($target, ['roles' => ['SuperAdmin']]));

            $response->assertSessionHasErrors(['roles.0' => __('You are not allowed to assign this role.')]);
            $this->assertFalse($target->fresh()->hasRole('SuperAdmin'));
        }
    }

    #[Test]
    public function company_admin_role_selector_offers_company_admin_but_not_super_admin(): void
    {
        $seller = $this->createUserWithRole('Seller', $this->companyAdmin->company_id);

        $response = $this->actingAs($this->companyAdmin)->get('/user/update/'.$seller->id);

        $response->assertOk();
        $response->assertSee('value="CompanyAdmin"', false);
        $response->assertSee('value="HR"', false);
        $response->assertDontSee('value="SuperAdmin"', false);
    }

    #[Test]
    public function company_admin_can_manage_another_company_admin_of_its_company(): void
    {
        $otherAdmin = $this->createUserWithRole('CompanyAdmin', $this->companyAdmin->company_id);

        $this->actingAs($this->companyAdmin)->get('/user/update/'.$otherAdmin->id)->assertOk();
        $this->actingAs($this->companyAdmin)->patch('/user/status/'.$otherAdmin->id, ['active' => 0])->assertRedirect('/user');

        $this->assertFalse($otherAdmin->fresh()->active);
    }

    #[Test]
    public function company_admin_cannot_manage_a_super_admin_even_in_its_company(): void
    {
        $superAdmin = $this->createUserWithRole('SuperAdmin', $this->companyAdmin->company_id);

        $this->actingAs($this->companyAdmin)->get('/user/update/'.$superAdmin->id)->assertForbidden();
        $this->actingAs($this->companyAdmin)->post('/user/save', $this->payloadFor($superAdmin, ['roles' => ['Seller']]))->assertForbidden();
        $this->actingAs($this->companyAdmin)->patch('/user/status/'.$superAdmin->id, ['active' => 0])->assertForbidden();
        $this->actingAs($this->companyAdmin, 'api')->putJson('/api/user/'.$superAdmin->id, [
            'first_name' => 'Hijacked',
            'last_name' => 'Account',
            'email' => $superAdmin->email,
            'lang' => 'es',
            'timezone' => 'Europe/Madrid',
        ])->assertForbidden();

        $this->assertTrue($superAdmin->fresh()->hasRole('SuperAdmin'));
        $this->assertTrue($superAdmin->fresh()->active);
        $this->assertNotSame('Hijacked', $superAdmin->fresh()->first_name);
    }

    #[Test]
    public function company_admin_cannot_manage_users_of_another_company(): void
    {
        $foreignUser = $this->createUserWithRole('Seller');

        $this->assertFalse($this->companyAdmin->canManageUser($foreignUser));
        $this->actingAs($this->companyAdmin)->post('/user/save', $this->payloadFor($foreignUser, ['roles' => ['Accountant']]))->assertForbidden();
        $this->assertFalse($foreignUser->fresh()->hasRole('Accountant'));
    }

    #[Test]
    public function company_admin_creates_users_in_its_company_but_never_as_super_admin(): void
    {
        $payload = [
            'first_name' => 'New',
            'last_name' => 'User',
            'lang' => 'es',
            'password' => 'initial-password',
            'password_confirmation' => 'initial-password',
        ];

        $this->actingAs($this->companyAdmin)->post('/user/save', $payload + ['email' => 'new.admin@example.com', 'roles' => ['CompanyAdmin']])
            ->assertRedirect('/user');
        $created = User::where('email', 'new.admin@example.com')->firstOrFail();
        $this->assertSame((int) $this->companyAdmin->company_id, (int) $created->company_id);
        $this->assertTrue($created->hasRole('CompanyAdmin'));

        $this->actingAs($this->companyAdmin)->from('/user/create')
            ->post('/user/save', $payload + ['email' => 'new.super@example.com', 'roles' => ['SuperAdmin']])
            ->assertSessionHasErrors(['roles.0' => __('You are not allowed to assign this role.')]);
        $this->assertDatabaseMissing('user', ['email' => 'new.super@example.com']);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payloadFor(User $user, array $overrides = []): array
    {
        return array_merge([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'lang' => 'es',
            'timezone' => 'Europe/Madrid',
        ], $overrides);
    }

    private function createUserWithRole(string $role, ?int $companyId = null): User
    {
        $user = User::factory()->create($companyId ? ['company_id' => $companyId] : []);
        $user->assignRole($role);

        return $user;
    }
}
