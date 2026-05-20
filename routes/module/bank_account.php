<?php

declare(strict_types=1);

use App\Http\Controllers\Bank\Account\BankAccountCreateController;
use App\Http\Controllers\Bank\Account\BankAccountDeleteController;
use App\Http\Controllers\Bank\Account\BankAccountIndexController;
use App\Http\Controllers\Bank\Account\BankAccountSaveController;
use App\Http\Controllers\Bank\Account\BankAccountUpdateController;
use Illuminate\Support\Facades\Route;

Route::get('/bank-account',
    [BankAccountIndexController::class, 'index'])
    ->can('read bank');

Route::get('/bank-account/create',
    [BankAccountCreateController::class, 'create'])
    ->can('create bank');

Route::get('/bank-account/update/{id}',
    [BankAccountUpdateController::class, 'update'])
    ->can('update bank');

Route::get('/bank-account/save',
    [BankAccountSaveController::class, 'save'])
    ->can('create bank');

Route::get('/bank-account/delete/{id}',
    [BankAccountDeleteController::class, 'delete'])
    ->can('delete bank');
