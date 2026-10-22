<?php

declare(strict_types=1);

use App\Http\Controllers\Branch\BranchCreateController;
use App\Http\Controllers\Branch\BranchDeleteController;
use App\Http\Controllers\Branch\BranchIndexController;
use App\Http\Controllers\Branch\BranchMainController;
use App\Http\Controllers\Branch\BranchSaveController;
use App\Http\Controllers\Branch\BranchUpdateController;
use Illuminate\Support\Facades\Route;

Route::get('/branch', [BranchIndexController::class, 'index'])->can('read company');
Route::get('/branch/create', [BranchCreateController::class, 'create'])->can('update company');
Route::get('/branch/update/{id}', [BranchUpdateController::class, 'update'])->can('update company');
Route::post('/branch/save', [BranchSaveController::class, 'save'])->can('update company');
Route::delete('/branch/delete/{id}', [BranchDeleteController::class, 'delete'])->can('update company');
Route::post('/branch/main/{id}', [BranchMainController::class, 'makeMain'])->can('update company');
