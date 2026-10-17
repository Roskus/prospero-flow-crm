<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\User;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class UserUpdateControllerTest extends TestCase
{
    #[Test]
    public function super_admin_can_edit_a_user_of_another_company(): void
    {
        $otherCompanyUser = User::factory()->create();

        $response = $this->get('/user/update/'.$otherCompanyUser->id);

        $response->assertOk();
        $response->assertViewIs('user.user');
        $response->assertSee($otherCompanyUser->email);
    }

    #[Test]
    public function non_super_admin_can_edit_a_user_of_own_company(): void
    {
        $companyAdmin = $this->companyAdmin();
        $colleague = User::factory()->create(['company_id' => $companyAdmin->company_id]);

        $response = $this->actingAs($companyAdmin)->get('/user/update/'.$colleague->id);

        $response->assertOk();
        $response->assertSee($colleague->email);
    }

    #[Test]
    public function non_super_admin_cannot_edit_a_user_of_another_company(): void
    {
        $otherCompanyUser = User::factory()->create();

        $response = $this->actingAs($this->companyAdmin())->get('/user/update/'.$otherCompanyUser->id);

        $response->assertNotFound();
    }

    private function companyAdmin(): User
    {
        $companyAdmin = User::factory()->create();
        $companyAdmin->assignRole('User');
        $companyAdmin->givePermissionTo(Permission::create(['name' => 'update user', 'guard_name' => 'web']));

        return $companyAdmin;
    }
}
