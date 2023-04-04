<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/supplier', [\App\Http\Controllers\Supplier\SupplierIndexController::class, 'index']);
Route::get('/supplier/create', [\App\Http\Controllers\Supplier\SupplierCreateController::class, 'create']);
Route::get('/supplier/update/{id}', [\App\Http\Controllers\Supplier\SupplierUpdateController::class, 'update']);
Route::post('/supplier/save', [\App\Http\Controllers\Supplier\SupplierSaveController::class, 'save']);
