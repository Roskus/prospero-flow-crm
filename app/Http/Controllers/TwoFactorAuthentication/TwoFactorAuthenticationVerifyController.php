<?php

namespace App\Http\Controllers\TwoFactorAuthentication;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PragmaRX\Google2FALaravel\Facade as Google2FA;

class TwoFactorAuthenticationVerifyController extends Controller
{
    public function verify(Request $request)
    {
        $key = decrypt(auth()->user()->two_factor_secret);

        $secret = $request->integer('one_time_password');
        $secret = preg_replace('/\s+/', '', $secret);

        if (! Google2FA::verifyKey($key, $secret)) {
            return redirect('/')->withErrors([__('The provided two factor authentication code was invalid.')]);
        }

        Google2FA::login();

        return redirect('/');
    }
}
