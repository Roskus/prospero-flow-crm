<?php

declare(strict_types=1);

use App\Http\Controllers\Currency\CurrencyIndexController;
use App\Http\Controllers\Currency\CurrencySaveController;
use Illuminate\Support\Facades\Route;

Route::get('/currency', [CurrencyIndexController::class, 'index'])->name('currency.index');
Route::post('/currency/save', [CurrencySaveController::class, 'save'])->name('currency.save');
