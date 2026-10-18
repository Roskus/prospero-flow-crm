<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\User;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
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
}
