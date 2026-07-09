<?php

declare(strict_types=1);

use App\Http\Controllers\Product\ProductCreateController;
use App\Http\Controllers\Product\ProductDeleteController;
use App\Http\Controllers\Product\ProductImportIndexController;
use App\Http\Controllers\Product\ProductImportSaveController;
use App\Http\Controllers\Product\ProductIndexController;
use App\Http\Controllers\Product\ProductSaveController;
use App\Http\Controllers\Product\ProductUpdateController;
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/product', [ProductIndexController::class, 'index'])->name('product.index')->can('read product');
Route::get('/product/create', [ProductCreateController::class, 'create'])->can('create product');
Route::get('/product/update/{id}', [ProductUpdateController::class, 'update'])->can('update product');
Route::post('/product/save', [ProductSaveController::class, 'save'])->can('create product')->can('update product');
Route::get('/product/import', [ProductImportIndexController::class, 'index'])->can('create product');
Route::post('/product/import/save', [ProductImportSaveController::class, 'save'])->can('create product');

Route::delete('/product/delete/{id}', [ProductDeleteController::class, 'delete'])
    ->can('delete product');
