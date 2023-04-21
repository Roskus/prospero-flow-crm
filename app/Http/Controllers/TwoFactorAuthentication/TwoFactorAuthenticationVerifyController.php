<?php

namespace App\Http\Controllers\TwoFactorAuthentication;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use PragmaRX\Google2FALaravel\Facade;

class TwoFactorAuthenticationVerifyController extends Controller
{
    public function verify(Request $request)
    {
        $key = decrypt(auth()->user()->two_factor_secret);

        $secret = $request->one_time_password;
        $secret = preg_replace('/\s+/', '', $secret);

        $google2fa = new Google2FA();

        $google2fa->setEnforceGoogleAuthenticatorCompatibility(false);

        if($google2fa->verifyKey($key, $secret)){
            Facade::login();
        }

        
        return redirect('/lead');
        
    }
}
