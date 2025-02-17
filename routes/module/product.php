<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/product', [\App\Http\Controllers\Product\ProductIndexController::class, 'index']);
Route::get('/product/create', [\App\Http\Controllers\Product\ProductCreateController::class, 'create']);
Route::get('/product/update/{id}', [\App\Http\Controllers\Product\ProductUpdateController::class, 'update']);
Route::post('/product/save', [\App\Http\Controllers\Product\ProductSaveController::class, 'save']);
Route::get('/product/import', [\App\Http\Controllers\Product\ProductImportIndexController::class, 'index']);
Route::post('/product/import/save', [\App\Http\Controllers\Product\ProductImportSaveController::class, 'save']);

Route::get('/product/delete/{id}', [\App\Http\Controllers\Product\ProductDeleteController::class, 'delete']);
