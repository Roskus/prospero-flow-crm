<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PasswordChangeControllerTest extends TestCase
{
    #[Test]
    public function user_with_must_change_flag_is_redirected_to_password_change_page(): void
    {
        $this->user->must_change_password = true;
        $this->user->save();

        $response = $this->get('/');

        $response->assertRedirect(route('password.change.form'));
    }

    #[Test]
    public function user_without_must_change_flag_is_not_redirected(): void
    {
        $this->user->must_change_password = false;
        $this->user->save();

        $response = $this->get('/');

        $response->assertOk();
    }

    #[Test]
    public function password_change_page_loads_for_user_with_must_change_flag(): void
    {
        $this->user->must_change_password = true;
        $this->user->save();

        $response = $this->get(route('password.change.form'));

        $response->assertOk();
        $response->assertSee(__('Change Password'));
    }

    #[Test]
    public function user_can_change_password_and_clear_must_change_flag(): void
    {
        $this->user->must_change_password = true;
        $this->user->password = Hash::make('old-password');
        $this->user->save();

        $response = $this->post(route('password.change.update'), [
            'current_password' => 'old-password',
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ]);

        $response->assertRedirect('/');
        $this->user->refresh();
        $this->assertTrue(Hash::check('new-secure-password', $this->user->password));
        $this->assertFalse($this->user->must_change_password);
    }

    #[Test]
    public function password_change_requires_valid_current_password(): void
    {
        $this->user->must_change_password = true;
        $this->user->password = Hash::make('old-password');
        $this->user->save();

        $response = $this->post(route('password.change.update'), [
            'current_password' => 'wrong-password',
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ]);

        $response->assertSessionHasErrors('current_password');
    }

    #[Test]
    public function password_change_requires_minimum_length(): void
    {
        $this->user->must_change_password = true;
        $this->user->password = Hash::make('old-password');
        $this->user->save();

        $response = $this->post(route('password.change.update'), [
            'current_password' => 'old-password',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    }

    #[Test]
    public function password_change_requires_confirmation(): void
    {
        $this->user->must_change_password = true;
        $this->user->password = Hash::make('old-password');
        $this->user->save();

        $response = $this->post(route('password.change.update'), [
            'current_password' => 'old-password',
            'password' => 'new-secure-password',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors('password');
    }
}
