<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\User;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserStatusControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([RoleSeeder::class, PermissionSeeder::class]);
    }

    #[Test]
    public function new_users_are_active_by_default(): void
    {
        $this->assertTrue(User::factory()->create()->fresh()->active);
    }

    #[Test]
    public function hr_can_deactivate_and_reactivate_a_regular_user(): void
    {
        $hr = $this->createUserWithRole('HR');
        $employee = $this->createUserWithRole('Seller', $hr->company_id);

        $this->actingAs($hr)->patch('/user/status/'.$employee->id, ['active' => 0])
            ->assertRedirect('/user')
            ->assertSessionHas('message', __('User deactivated'));
        $this->assertFalse($employee->fresh()->active);

        $this->actingAs($hr)->patch('/user/status/'.$employee->id, ['active' => 1])->assertRedirect('/user');
        $this->assertTrue($employee->fresh()->active);
    }

    #[Test]
    public function hr_cannot_change_the_status_of_administrators(): void
    {
        $hr = $this->createUserWithRole('HR');
        $companyAdmin = $this->createUserWithRole('CompanyAdmin', $hr->company_id);

        $this->actingAs($hr)->patch('/user/status/'.$companyAdmin->id, ['active' => 0])->assertForbidden();
        $this->assertTrue($companyAdmin->fresh()->active);
    }

    #[Test]
    public function users_cannot_deactivate_themselves(): void
    {
        $hr = $this->createUserWithRole('HR');

        $this->actingAs($hr)->patch('/user/status/'.$hr->id, ['active' => 0])->assertForbidden();
        $this->assertTrue($hr->fresh()->active);
    }

    #[Test]
    public function status_cannot_be_changed_for_users_of_another_company(): void
    {
        $hr = $this->createUserWithRole('HR');
        $foreignUser = $this->createUserWithRole('Seller');

        $this->actingAs($hr)->patch('/user/status/'.$foreignUser->id, ['active' => 0])->assertForbidden();
        $this->assertTrue($foreignUser->fresh()->active);
    }

    #[Test]
    public function seller_cannot_change_statuses(): void
    {
        $seller = $this->createUserWithRole('Seller');
        $employee = $this->createUserWithRole('Seller', $seller->company_id);

        $this->actingAs($seller)->patch('/user/status/'.$employee->id, ['active' => 0])->assertForbidden();
    }

    #[Test]
    public function it_validates_the_status(): void
    {
        $employee = $this->createUserWithRole('Seller');

        $this->from('/user')->patch('/user/status/'.$employee->id, ['active' => 'maybe'])
            ->assertSessionHasErrors(['active' => __('The status is not valid.')]);
    }

    #[Test]
    public function user_list_shows_the_status_and_the_toggle(): void
    {
        $employee = $this->createUserWithRole('Seller', $this->user->company_id);
        $employee->forceFill(['active' => false])->save();

        $response = $this->get('/user');

        $response->assertOk();
        $response->assertSee(__('Inactive'));
        $response->assertSee('action="'.url('/user/status/'.$employee->id).'"', false);
    }

    #[Test]
    public function inactive_users_cannot_log_in_on_the_web(): void
    {
        $this->app['auth']->forgetGuards();
        $user = User::factory()->create(['password' => Hash::make('secret-password'), 'active' => false]);

        $this->post('/login', ['email' => $user->email, 'password' => 'secret-password'])->assertSessionHasErrors('email');
        $this->assertGuest('web');
    }

    #[Test]
    public function inactive_users_cannot_get_an_api_token(): void
    {
        $this->app['auth']->forgetGuards();
        $user = User::factory()->create(['password' => Hash::make('secret-password'), 'active' => false]);

        $this->postJson('/api/auth/login', ['email' => $user->email, 'password' => 'secret-password'])->assertUnauthorized();
    }

    #[Test]
    public function an_open_web_session_ends_when_the_user_is_deactivated(): void
    {
        $user = $this->createUserWithRole('Seller');
        $user->forceFill(['active' => false])->save();

        $response = $this->actingAs($user->fresh())->get('/');

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['email' => __('Your account is inactive.')]);
        $this->assertGuest('web');
    }

    #[Test]
    public function an_existing_api_token_is_rejected_when_the_user_is_deactivated(): void
    {
        $user = $this->createUserWithRole('Seller');
        $user->forceFill(['active' => false])->save();

        $this->actingAs($user->fresh(), 'api')->getJson('/api/customer')->assertForbidden()->assertJson(['error' => 'Inactive user']);
    }

    private function createUserWithRole(string $role, ?int $companyId = null): User
    {
        $user = User::factory()->create($companyId ? ['company_id' => $companyId] : []);
        $user->assignRole($role);

        return $user;
    }
}
