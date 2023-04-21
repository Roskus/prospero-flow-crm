<?php

use App\Http\Controllers\TwoFactorAuthentication\TwoFactorAuthenticationEnableController;
use App\Http\Controllers\TwoFactorAuthentication\TwoFactorAuthenticationVerifyController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('two-factor-authentication')->group(function () {

    Route::get('/enable', [TwoFactorAuthenticationEnableController::class, 'enable']);
    Route::get('/disable', [TwoFactorAuthenticationEnableController::class, 'disable']);

    Route::post('/verify', [TwoFactorAuthenticationVerifyController::class, 'verify']);

    Route::get('/regenerate-recovery', [TwoFactorAuthenticationRegenerateRecoveryController::class, 'regenerate']);
});
