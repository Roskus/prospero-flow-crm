<?php

use Illuminate\Support\Facades\Route;

Route::get('/bank-account',
    [\App\Http\Controllers\Bank\Account\BankAccountIndexController::class, 'index'])
    ->can('read bank');

Route::get('/bank-account/create',
    [\App\Http\Controllers\Bank\Account\BankAccountCreateController::class, 'create'])
    ->can('create bank');
