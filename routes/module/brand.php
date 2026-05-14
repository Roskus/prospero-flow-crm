<?php

declare(strict_types=1);

use App\Http\Controllers\Brand\BrandCreateController;
use App\Http\Controllers\Brand\BrandDeleteController;
use App\Http\Controllers\Brand\BrandIndexController;
use App\Http\Controllers\Brand\BrandSaveController;
use App\Http\Controllers\Brand\BrandUpdateController;
use Illuminate\Support\Facades\Route;

Route::get('/brand', [BrandIndexController::class, 'index']);
Route::get('/brand/create', [BrandCreateController::class, 'create']);
Route::get('/brand/update/{id}', [BrandUpdateController::class, 'update']);
Route::get('/brand/delete/{id}', [BrandDeleteController::class, 'delete']);
Route::post('/brand/save', [BrandSaveController::class, 'save']);
