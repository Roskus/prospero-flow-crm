<?php

declare(strict_types=1);

use App\Http\Controllers\Invoice\InvoiceCancelController;
use App\Http\Controllers\Invoice\InvoiceCreateController;
use App\Http\Controllers\Invoice\InvoiceIndexController;
use App\Http\Controllers\Invoice\InvoicePaymentController;
use App\Http\Controllers\Invoice\InvoicePdfController;
use App\Http\Controllers\Invoice\InvoiceSaveController;
use App\Http\Controllers\Invoice\InvoiceShowController;
use Illuminate\Support\Facades\Route;

Route::get('/invoice', [InvoiceIndexController::class, 'index'])
    ->name('invoice.index')
    ->can('read invoice');
Route::get('/invoice/create/{order_number}', [InvoiceCreateController::class, 'create'])
    ->name('invoice.create')
    ->can('create invoice');
Route::post('/invoice/save', [InvoiceSaveController::class, 'save'])
    ->name('invoice.save')
    ->can('create invoice');
Route::get('/invoice/show/{id}', [InvoiceShowController::class, 'show'])
    ->name('invoice.show')
    ->can('read invoice');
Route::post('/invoice/cancel/{id}', [InvoiceCancelController::class, 'cancel'])
    ->name('invoice.cancel')
    ->can('delete invoice');
Route::get('/invoice/download/{id}', [InvoicePdfController::class, 'download'])
    ->name('invoice.download')
    ->can('read invoice');
Route::post('/invoice/payment/{id}', [InvoicePaymentController::class, 'store'])
    ->name('invoice.payment')
    ->can('create payment');
