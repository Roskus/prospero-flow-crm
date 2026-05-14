<?php

declare(strict_types=1);

use App\Http\Controllers\Order\OrderConfirmController;
use App\Http\Controllers\Order\OrderCreateController;
use App\Http\Controllers\Order\OrderDeleteController;
use App\Http\Controllers\Order\OrderIndexController;
use App\Http\Controllers\Order\OrderPdfController;
use App\Http\Controllers\Order\OrderSaveController;
use App\Http\Controllers\Order\OrderShowController;
use App\Http\Controllers\Order\OrderUpdateController;
use Illuminate\Support\Facades\Route;

Route::get('/order', [OrderIndexController::class, 'index']);
Route::get('/order/create', [OrderCreateController::class, 'create']);
Route::get('/order/show/{id}', [OrderShowController::class, 'show']);
Route::get('/order/update/{id}', [OrderUpdateController::class, 'update']);
Route::get('/order/delete/{id}', [OrderDeleteController::class, 'delete']);
Route::post('/order/save', [OrderSaveController::class, 'save']);
Route::get('/order/download/{id}', [OrderPdfController::class, 'download'])
    ->name('order-download');
Route::get('/order/confirm/{id}', [OrderConfirmController::class, 'confirm']);
