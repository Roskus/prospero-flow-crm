<?php

declare(strict_types=1);

use App\Http\Controllers\User\UserCreateController;
use App\Http\Controllers\User\UserDeleteController;
use App\Http\Controllers\User\UserListController;
use App\Http\Controllers\User\UserSaveController;
use App\Http\Controllers\User\UserUpdateController;
use Illuminate\Support\Facades\Route;

Route::get('/user', [UserListController::class, 'index']);
Route::get('/user/create', [UserCreateController::class, 'create']);
Route::get('/user/update/{id}', [UserUpdateController::class, 'update']);
Route::post('/user/save', [UserSaveController::class, 'save']);
Route::get('/user/delete/{id}', [UserDeleteController::class, 'delete']);
