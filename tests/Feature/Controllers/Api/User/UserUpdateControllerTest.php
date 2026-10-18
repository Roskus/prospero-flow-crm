<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserUpdateControllerTest extends TestCase
{
    #[Test]
    public function company_admin_can_update_a_user_of_the_same_company(): void
    {
        $companyAdmin = $this->createCompanyAdmin();
        $user = User::factory()->create(['company_id' => $companyAdmin->company_id]);

        $response = $this->actingAs($companyAdmin, 'api')
            ->putJson("/api/user/{$user->id}", $this->payload(['first_name' => 'Updated']));

        $response->assertOk();
        $this->assertEquals('Updated', $user->fresh()->first_name);
    }

    #[Test]
    public function company_admin_cannot_take_over_a_super_admin_of_the_same_company(): void
    {
        $companyAdmin = $this->createCompanyAdmin();
        $superAdmin = User::factory()->create(['company_id' => $companyAdmin->company_id]);
        $superAdmin->assignRole('SuperAdmin');
        $originalPassword = $superAdmin->password;

        $response = $this->actingAs($companyAdmin, 'api')
            ->putJson("/api/user/{$superAdmin->id}", $this->payload(['password' => 'hijacked-password']));

        $response->assertForbidden();
        $this->assertEquals($originalPassword, $superAdmin->fresh()->password);
        $this->assertFalse(Hash::check('hijacked-password', $superAdmin->fresh()->password));
    }

    #[Test]
    public function seller_cannot_update_users(): void
    {
        $seller = User::factory()->create();
        $seller->assignRole(Role::findOrCreate('Seller', 'web'));
        $user = User::factory()->create(['company_id' => $seller->company_id]);

        $response = $this->actingAs($seller, 'api')
            ->putJson("/api/user/{$user->id}", $this->payload());

        $response->assertForbidden();
    }

    /**
     * @param  array<string, string>  $overrides
     * @return array<string, string>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => fake()->unique()->safeEmail(),
            'lang' => 'en',
            'timezone' => 'Europe/Madrid',
        ], $overrides);
    }

    private function createCompanyAdmin(): User
    {
        $role = Role::findOrCreate('CompanyAdmin', 'web');
        $role->givePermissionTo(Permission::findOrCreate('update user', 'web'));

        $companyAdmin = User::factory()->create();
        $companyAdmin->assignRole($role);

        return $companyAdmin;
    }
}
