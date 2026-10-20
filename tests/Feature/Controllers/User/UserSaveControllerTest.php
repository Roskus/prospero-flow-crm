<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\User;

use App\Models\Company;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserSaveControllerTest extends TestCase
{
    #[Test]
    public function it_can_save_a_user()
    {
        $response = $this->post('user/save', []);
        $response->assertSessionHasErrors(['first_name', 'email', 'lang']);

        $data = [
            'first_name' => fake()->name(),
            'last_name' => fake()->name(),
            'email' => fake()->email(),
            'lang' => 'en',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'company_id' => $this->user->company_id,
        ];
        $response = $this->post('user/save', $data);
        $response->assertRedirect('/user');

        $response = $this->get('/user');
        $response->assertSee($data['first_name']);
        $response->assertSee($data['last_name']);
        $response->assertSee($data['email']);
    }

    #[Test]
    public function super_admin_can_update_a_user_of_another_company(): void
    {
        $otherCompanyUser = User::factory()->create();

        $response = $this->post('/user/save', [
            'id' => $otherCompanyUser->id,
            'first_name' => 'Updated',
            'last_name' => $otherCompanyUser->last_name,
            'email' => $otherCompanyUser->email,
            'lang' => 'es',
            'company_id' => $otherCompanyUser->company_id,
        ]);

        $response->assertRedirect('/user');
        $this->assertDatabaseHas('user', [
            'id' => $otherCompanyUser->id,
            'first_name' => 'Updated',
            'company_id' => $otherCompanyUser->company_id,
        ]);
    }

    #[Test]
    public function it_requires_a_password_when_creating_a_user(): void
    {
        $response = $this->from('/user/create')->post('/user/save', [
            'first_name' => 'No',
            'last_name' => 'Password',
            'email' => 'no.password@example.com',
            'lang' => 'es',
        ]);

        $response->assertSessionHasErrors(['password' => __('The password is required for new users.')]);
        $this->assertDatabaseMissing('user', ['email' => 'no.password@example.com']);
    }

    #[Test]
    public function it_rejects_a_password_confirmation_that_does_not_match(): void
    {
        $response = $this->from('/user/create')->post('/user/save', [
            'first_name' => 'Wrong',
            'last_name' => 'Confirmation',
            'email' => 'wrong.confirmation@example.com',
            'lang' => 'es',
            'password' => 'initial-password',
            'password_confirmation' => 'another-password',
        ]);

        $response->assertSessionHasErrors(['password' => __('The password confirmation does not match.')]);
    }

    #[Test]
    public function super_admin_created_owner_of_a_new_company_can_log_in(): void
    {
        $company = Company::factory()->create();
        Role::findOrCreate('CompanyAdmin', 'web');

        $this->post('/user/save', [
            'first_name' => 'New',
            'last_name' => 'Owner',
            'email' => 'new.owner@example.com',
            'lang' => 'es',
            'password' => 'Owner-pass-123',
            'password_confirmation' => 'Owner-pass-123',
            'company_id' => $company->id,
            'roles' => ['CompanyAdmin'],
        ])->assertRedirect('/user');

        $owner = User::query()->where('email', 'new.owner@example.com')->firstOrFail();
        $this->assertSame($company->id, $owner->company_id);
        $this->assertTrue($owner->active);
        $this->assertTrue($owner->hasRole('CompanyAdmin'));

        $this->app['auth']->forgetGuards();
        $this->post('/login', ['email' => 'new.owner@example.com', 'password' => 'Owner-pass-123'])
            ->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($owner);
    }

    #[Test]
    public function it_keeps_the_entered_data_when_the_password_confirmation_fails(): void
    {
        $company = Company::factory()->create();
        Role::findOrCreate('CompanyAdmin', 'web');

        $response = $this->from('/user/create')->followingRedirects()->post('/user/save', [
            'first_name' => 'Kept',
            'last_name' => 'Values',
            'email' => 'kept.values@example.com',
            'phone' => '600111222',
            'lang' => 'es',
            'password' => 'Owner-pass-123',
            'password_confirmation' => 'Different-123',
            'company_id' => $company->id,
            'roles' => ['CompanyAdmin'],
        ]);

        $response->assertOk();
        $response->assertSee(__('The password confirmation does not match.'));
        $response->assertSee('value="Kept"', false);
        $response->assertSee('value="Values"', false);
        $response->assertSee('value="kept.values@example.com"', false);
        $response->assertSee('value="600111222"', false);
        $response->assertSee('<option value="es" selected>', false);
        $response->assertSee('<option value="'.$company->id.'" selected>', false);
        $response->assertSee('<option value="CompanyAdmin" selected>', false);
        $response->assertDontSee('Owner-pass-123');
        $response->assertSee('autocomplete="new-password"', false);
        $this->assertDatabaseMissing('user', ['email' => 'kept.values@example.com']);
    }
}
