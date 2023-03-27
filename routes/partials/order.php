<?php

use Illuminate\Support\Facades\Route;

Route::get('/order', [\App\Http\Controllers\Order\OrderIndexController::class, 'index']);
Route::get('/order/create', [\App\Http\Controllers\Order\OrderCreateController::class, 'create']);
Route::get('/order/show/{id}', [\App\Http\Controllers\Order\OrderShowController::class, 'show']);
Route::get('/order/update/{id}', [\App\Http\Controllers\Order\OrderUpdateController::class, 'update']);
Route::get('/order/delete/{id}', [\App\Http\Controllers\Order\OrderDeleteController::class, 'delete']);
Route::post('/order/save', [\App\Http\Controllers\Order\OrderSaveController::class, 'save']);
Route::get('/order/download/{id}', [\App\Http\Controllers\Order\OrderPdfController::class, 'download'])
    ->name('order-download');
Route::get('/order/confirm/{id}', [\App\Http\Controllers\Order\OrderConfirmController::class, 'confirm']);
