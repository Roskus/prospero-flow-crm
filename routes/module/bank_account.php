<?php

declare(strict_types=1);

use App\Http\Controllers\Bank\Account\BankAccountCreateController;
use App\Http\Controllers\Bank\Account\BankAccountDeleteController;
use App\Http\Controllers\Bank\Account\BankAccountIndexController;
use App\Http\Controllers\Bank\Account\BankAccountSaveController;
use App\Http\Controllers\Bank\Account\BankAccountShowController;
use App\Http\Controllers\Bank\Account\BankAccountUpdateController;
use App\Http\Controllers\BankCard\BankCardDeleteController;
use App\Http\Controllers\BankCard\BankCardFormController;
use App\Http\Controllers\BankCard\BankCardSaveController;
use Illuminate\Support\Facades\Route;

Route::get('/bank-account', [BankAccountIndexController::class, 'index'])->can('read accounting');
Route::get('/bank-account/show/{id}', [BankAccountShowController::class, 'show'])->can('read accounting');
Route::get('/bank-account/create', [BankAccountCreateController::class, 'create'])->can('create accounting');
Route::get('/bank-account/update/{id}', [BankAccountUpdateController::class, 'update'])->can('update accounting');
Route::post('/bank-account/save', [BankAccountSaveController::class, 'save'])->can('create accounting');
Route::delete('/bank-account/delete/{id}', [BankAccountDeleteController::class, 'delete'])->can('delete accounting');

// Bank cards
Route::get('/bank-card/create/{bankAccountId}', [BankCardFormController::class, 'create'])->can('create accounting');
Route::get('/bank-card/edit/{id}', [BankCardFormController::class, 'edit'])->can('update accounting');
Route::post('/bank-card/save', [BankCardSaveController::class, 'save'])->can('create accounting');
Route::delete('/bank-card/delete/{id}', [BankCardDeleteController::class, 'delete'])->can('delete accounting');
