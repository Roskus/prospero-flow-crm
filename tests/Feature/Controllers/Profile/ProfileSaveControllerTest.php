<?php

namespace Tests\Feature\Controllers\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileSaveControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_not_save_profile_without_data()
    {
        $user = $this->signIn();
        $this->actingAs($user);

        $response = $this->post('profile/save', []);
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function it_can_save_profile()
    {
        $user = $this->signIn();
        $this->actingAs($user);

        $data = [
            'first_name' => fake()->name(),
            'last_name' => fake()->name(),
            'email' => fake()->email(),
            'lang' => fake()->randomElement(array_keys(config('app.locales'))),
        ];
        $response = $this->post('profile/save', $data);
        $response->assertRedirect('/profile');

        $response = $this->get('/profile');
        $response->assertSee($data['first_name']);
        $response->assertSee($data['last_name']);
        $response->assertSee($data['email']);
    }

    /** @test */
    public function it_can_update_password()
    {
        $user = $this->signIn();
        $this->actingAs($user);

        $oldPassword = $user->password;
        $newPassword = fake()->password(8);
        $data = [
            'first_name' => fake()->name(),
            'last_name' => fake()->name(),
            'email' => fake()->email(),
            'lang' => fake()->randomElement(array_keys(config('app.locales'))),
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ];
        $response = $this->post('profile/save', $data);

        $this->assertNotEquals($oldPassword, User::first()->password);
    }
}
