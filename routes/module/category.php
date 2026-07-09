<?php

declare(strict_types=1);

use App\Http\Controllers\Category\CategoryCreateController;
use App\Http\Controllers\Category\CategoryDeleteController;
use App\Http\Controllers\Category\CategoryIndexController;
use App\Http\Controllers\Category\CategorySaveController;
use App\Http\Controllers\Category\CategoryUpdateController;
use Illuminate\Support\Facades\Route;

Route::get('/category', [CategoryIndexController::class, 'index'])->can('read category');
Route::get('/category/create', [CategoryCreateController::class, 'create'])->can('create category');
Route::get('/category/update/{id}', [CategoryUpdateController::class, 'update'])->can('update category');
Route::get('/category/delete/{id}', [CategoryDeleteController::class, 'delete'])->can('delete category');
Route::post('/category/save', [CategorySaveController::class, 'save'])->can('create category')->can('update category');
