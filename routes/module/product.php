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

Route::match(['get', 'post'], '/product', [ProductIndexController::class, 'index'])->name('product.index');
Route::get('/product/create', [ProductCreateController::class, 'create']);
Route::get('/product/update/{id}', [ProductUpdateController::class, 'update']);
Route::post('/product/save', [ProductSaveController::class, 'save']);
Route::get('/product/import', [ProductImportIndexController::class, 'index']);
Route::post('/product/import/save', [ProductImportSaveController::class, 'save']);

Route::delete('/product/delete/{id}', [ProductDeleteController::class, 'delete'])
    ->can('delete product');
