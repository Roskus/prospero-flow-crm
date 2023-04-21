<?php

namespace App\Http\Controllers\TwoFactorAuthentication;

use App\Http\Controllers\Controller;
use PragmaRX\Google2FAQRCode\Google2FA;
use PragmaRX\Recovery\Recovery;

class TwoFactorAuthenticationEnableController extends Controller
{
    public function enable()
    {

        $user = auth()->user();

        if ($user->two_factor_secret = ! null) {
        return back()->with('status', __('Two factor authentication is already enabled.'));
        }

        $google2fa = new Google2FA();
        $recovery = new Recovery();

        $key = $google2fa->generateSecretKey();

        $user->forceFill([
            'two_factor_secret' => encrypt($key),
            'two_factor_recovery_codes' => encrypt(json_encode($recovery->toJson())),
        ])->save();

        $QRCodeInline = $google2fa->getQRCodeInline(
            config('app.name', $user->first_name),
            $user->email,
            $key
        );

        return view('twofactorauthentication.enable', compact('QRCodeInline'));
    }
}
