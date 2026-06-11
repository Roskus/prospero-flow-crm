<?php

declare(strict_types=1);

use App\Http\Controllers\Account\Category\AccountCategoryDeleteController;
use App\Http\Controllers\Account\Category\AccountCategoryIndexController;
use App\Http\Controllers\Account\Category\AccountCategorySaveController;
use Illuminate\Support\Facades\Route;

Route::get('/account/category', [AccountCategoryIndexController::class, 'index']);
Route::post('/account/category/save', [AccountCategorySaveController::class, 'save']);
Route::delete('/account/category/delete/{id}', [AccountCategoryDeleteController::class, 'delete']);
