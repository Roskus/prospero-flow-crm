<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/category', [\App\Http\Controllers\Category\CategoryIndexController::class, 'index']);
Route::get('/category/create', [\App\Http\Controllers\Category\CategoryCreateController::class, 'create']);
Route::get('/category/update/{id}', [\App\Http\Controllers\Category\CategoryUpdateController::class, 'update']);
Route::get('/category/delete/{id}', [\App\Http\Controllers\Category\CategoryDeleteController::class, 'delete']);
Route::post('/category/save', [\App\Http\Controllers\Category\CategorySaveController::class, 'save']);
