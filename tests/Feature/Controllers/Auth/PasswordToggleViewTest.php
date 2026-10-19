<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Auth;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PasswordToggleViewTest extends TestCase
{
    #[Test]
    public function login_screen_renders_password_toggle_inside_input_group_and_favicon(): void
    {
        auth()->guard('web')->logout();

        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('<link rel="icon" href="/favicon.png" type="image/png">', false);
        $response->assertSee('class="input-group input-group-lg has-validation"', false);
        $response->assertSee('class="toggle-password btn btn-outline-secondary"', false);
        $response->assertDontSee('field-icon', false);
    }

    #[Test]
    public function lock_screen_renders_password_toggle_inside_input_group_and_favicon(): void
    {
        $response = $this->get('/lock');

        $response->assertOk();
        $response->assertSee('<link rel="icon" href="/favicon.png" type="image/png">', false);
        $response->assertSee('class="toggle-password btn btn-outline-secondary"', false);
        $response->assertSee('aria-label="Unlock"', false);
        $response->assertDontSee('field-icon', false);
    }

    #[Test]
    public function lock_screen_redirects_guests_to_login(): void
    {
        auth()->guard('web')->logout();

        $this->get('/lock')->assertRedirect(route('login'));
    }

    #[Test]
    public function change_password_screen_uses_shared_password_toggle(): void
    {
        $this->user->update(['must_change_password' => true]);

        $response = $this->get(route('password.change.form'));

        $response->assertOk();
        $response->assertSee('/asset/js/Password.js', false);
        $response->assertSee('class="toggle-password btn btn-outline-secondary"', false);
        $response->assertDontSee('$(".toggle-password").click', false);
    }

    #[Test]
    public function profile_screen_uses_shared_password_toggle(): void
    {
        $response = $this->get('/profile');

        $response->assertOk();
        $response->assertSee('/asset/js/Password.js', false);
        $response->assertSee('class="toggle-password btn btn-outline-secondary"', false);
        $response->assertDontSee('$(".toggle-password").click', false);
    }
}
