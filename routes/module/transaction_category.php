<?php

declare(strict_types=1);

use App\Http\Controllers\Transaction\Category\TransactionCategoryDeleteController;
use App\Http\Controllers\Transaction\Category\TransactionCategoryIndexController;
use App\Http\Controllers\Transaction\Category\TransactionCategorySaveController;
use Illuminate\Support\Facades\Route;

Route::get('/transaction/category', [TransactionCategoryIndexController::class, 'index']);
Route::post('/transaction/category/save', [TransactionCategorySaveController::class, 'save']);
Route::delete('/transaction/category/delete/{id}', [TransactionCategoryDeleteController::class, 'delete']);
