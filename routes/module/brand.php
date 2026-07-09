<?php

declare(strict_types=1);

use App\Http\Controllers\Brand\BrandCreateController;
use App\Http\Controllers\Brand\BrandDeleteController;
use App\Http\Controllers\Brand\BrandIndexController;
use App\Http\Controllers\Brand\BrandSaveController;
use App\Http\Controllers\Brand\BrandUpdateController;
use Illuminate\Support\Facades\Route;

Route::get('/brand', [BrandIndexController::class, 'index'])->can('read brand');
Route::get('/brand/create', [BrandCreateController::class, 'create'])->can('create brand');
Route::get('/brand/update/{id}', [BrandUpdateController::class, 'update'])->can('update brand');
Route::get('/brand/delete/{id}', [BrandDeleteController::class, 'delete'])->can('delete brand');
Route::post('/brand/save', [BrandSaveController::class, 'save'])->can('create brand')->can('update brand');
