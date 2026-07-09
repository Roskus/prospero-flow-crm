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

Route::get('/order', [OrderIndexController::class, 'index'])->can('read order');
Route::get('/order/create', [OrderCreateController::class, 'create'])->can('create order');
Route::get('/order/show/{order_number}', [OrderShowController::class, 'show'])->can('read order');
Route::get('/order/update/{order_number}', [OrderUpdateController::class, 'update'])->can('update order');
Route::get('/order/delete/{order_number}', [OrderDeleteController::class, 'delete'])->can('delete order');
Route::post('/order/save', [OrderSaveController::class, 'save'])->can('create order')->can('update order');
Route::get('/order/download/{order_number}', [OrderPdfController::class, 'download'])
    ->name('order-download')->can('read order');
Route::get('/order/confirm/{order_number}', [OrderConfirmController::class, 'confirm'])->can('update order');
