<?php

namespace Tests\Feature\Controllers\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileSaveControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_save_profile()
    {
        $user = $this->signIn();
        $this->actingAs($user);

        $response = $this->post('profile/save', []);
        $response->assertSessionHasErrors();

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

        // UPDATE PASSWORD
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
