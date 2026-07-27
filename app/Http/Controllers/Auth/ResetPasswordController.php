<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    protected string $redirectTo = RouteServiceProvider::HOME;

    protected function resetPassword($user, $password): void
    {
        $user->password = Hash::make($password);
        $user->must_change_password = false;
        $user->save();

        $user->setRememberToken(Str::random(60));

        event(new PasswordReset($user));

        $this->guard()->login($user);
    }
}
