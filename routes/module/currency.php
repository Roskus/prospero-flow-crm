<?php

declare(strict_types=1);

use App\Http\Controllers\Currency\CurrencyIndexController;
use App\Http\Controllers\Currency\CurrencySaveController;
use Illuminate\Support\Facades\Route;

Route::get('/currency', [CurrencyIndexController::class, 'index'])->name('currency.index')->can('read currency');
Route::post('/currency/save', [CurrencySaveController::class, 'save'])->name('currency.save')->can('create currency')->can('update currency');
