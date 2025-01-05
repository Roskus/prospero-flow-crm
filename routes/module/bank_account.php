<?php

use Illuminate\Support\Facades\Route;

Route::get('/bank-account',
    [\App\Http\Controllers\Bank\Account\BankAccountIndexController::class, 'index'])
    ->can('read bank');

Route::get('/bank-account/create',
    [\App\Http\Controllers\Bank\Account\BankAccountCreateController::class, 'create'])
    ->can('create bank');

Route::get('/bank-account/update/{id}',
    [\App\Http\Controllers\Bank\Account\BankAccountUpdateController::class, 'update'])
    ->can('update bank');

Route::get('/bank-account/save',
    [\App\Http\Controllers\Bank\Account\BankAccountSaveController::class, 'save'])
    ->can('create bank');

