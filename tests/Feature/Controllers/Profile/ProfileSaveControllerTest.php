<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Profile;

use App\Http\Middleware\MustChangePassword;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProfileSaveControllerTest extends TestCase
{
    #[Test]
    public function it_can_not_save_profile_without_data()
    {
        $response = $this->post('profile/save', []);
        $response->assertSessionHasErrors();
    }

    #[Test]
    public function it_can_save_profile()
    {
        $data = [
            'first_name' => fake()->name(),
            'last_name' => fake()->name(),
            'email' => fake()->email(),
            'lang' => 'en',
        ];
        $response = $this->post('profile/save', $data);
        $response->assertRedirect('/profile');

        $response = $this->get('/profile');
        $response->assertSee($data['first_name']);
        $response->assertSee($data['last_name']);
        $response->assertSee($data['email']);
    }

    #[Test]
    public function it_can_update_password()
    {
        $oldPassword = $this->user->password;
        $newPassword = fake()->password(8);
        $data = [
            'first_name' => fake()->name(),
            'last_name' => fake()->name(),
            'email' => fake()->email(),
            'lang' => 'en',
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ];
        $response = $this->post('profile/save', $data);

        $this->assertNotEquals($oldPassword, User::first()->password);
    }

    #[Test]
    public function it_clears_must_change_password_when_password_provided(): void
    {
        $this->user->must_change_password = true;
        $this->user->save();

        $newPassword = 'NewPass123!';

        $this->withoutMiddleware(MustChangePassword::class)
            ->post('profile/save', [
                'first_name' => fake()->name(),
                'last_name' => fake()->name(),
                'email' => fake()->email(),
                'lang' => 'en',
                'password' => $newPassword,
                'password_confirmation' => $newPassword,
            ]);

        $this->user->refresh();
        $this->assertFalse($this->user->must_change_password);
    }

    #[Test]
    public function it_preserves_must_change_password_when_password_empty(): void
    {
        $this->user->must_change_password = true;
        $this->user->save();

        $this->withoutMiddleware(MustChangePassword::class)
            ->post('profile/save', [
                'first_name' => fake()->name(),
                'last_name' => fake()->name(),
                'email' => fake()->email(),
                'lang' => 'en',
            ]);

        $this->user->refresh();
        $this->assertTrue($this->user->must_change_password);
    }
}
