<?php

use Illuminate\Support\Facades\Route;

Route::get('/brand', [\App\Http\Controllers\Brand\BrandIndexController::class, 'index']);
Route::get('/brand/create', [\App\Http\Controllers\Brand\BrandCreateController::class, 'create']);
Route::get('/brand/update/{id}', [\App\Http\Controllers\Brand\BrandUpdateController::class, 'update']);
Route::get('/brand/delete/{id}', [\App\Http\Controllers\Brand\BrandDeleteController::class, 'delete']);
Route::post('/brand/save', [\App\Http\Controllers\Brand\BrandSaveController::class, 'save']);
