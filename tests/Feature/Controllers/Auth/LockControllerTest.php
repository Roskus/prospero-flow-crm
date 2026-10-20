<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LockControllerTest extends TestCase
{
    #[Test]
    public function lock_screen_offers_signing_in_with_another_user(): void
    {
        $response = $this->get('/lock');

        $response->assertOk();
        $response->assertSee('action="'.route('logout').'"', false);
        $response->assertSee(__('Not :name?', ['name' => $this->user->first_name]));
        $response->assertSee(__('Sign in with another user'));
    }

    #[Test]
    public function a_locked_session_can_log_out_and_sign_in_with_another_user(): void
    {
        $this->get('/lock')->assertSessionHas('locked', true);

        $this->post(route('logout'))->assertRedirect('/');
        $this->assertGuest('web');
        $this->assertFalse(session('locked', false));

        $otherUser = User::factory()->create(['password' => Hash::make('other-password')]);

        $this->post('/login', ['email' => $otherUser->email, 'password' => 'other-password'])
            ->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($otherUser);

        $this->get('/')->assertOk();
    }
}
