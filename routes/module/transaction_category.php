<?php

declare(strict_types=1);

use App\Http\Controllers\Transaction\Category\TransactionCategoryDeleteController;
use App\Http\Controllers\Transaction\Category\TransactionCategoryIndexController;
use App\Http\Controllers\Transaction\Category\TransactionCategorySaveController;
use Illuminate\Support\Facades\Route;

Route::get('/transaction/category', [TransactionCategoryIndexController::class, 'index'])->can('read transaction');
Route::post('/transaction/category/save', [TransactionCategorySaveController::class, 'save'])->can('create transaction');
Route::delete('/transaction/category/delete/{id}', [TransactionCategoryDeleteController::class, 'delete'])->can('delete transaction');
