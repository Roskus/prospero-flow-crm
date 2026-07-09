<?php

declare(strict_types=1);

use App\Http\Controllers\User\UserCreateController;
use App\Http\Controllers\User\UserDeleteController;
use App\Http\Controllers\User\UserListController;
use App\Http\Controllers\User\UserSaveController;
use App\Http\Controllers\User\UserUpdateController;
use Illuminate\Support\Facades\Route;

Route::get('/user', [UserListController::class, 'index'])->can('read user');
Route::get('/user/create', [UserCreateController::class, 'create'])->can('create user');
Route::get('/user/update/{id}', [UserUpdateController::class, 'update'])->can('update user');
Route::post('/user/save', [UserSaveController::class, 'save'])->can('create user')->can('update user');
Route::delete('/user/delete/{id}', [UserDeleteController::class, 'delete'])->can('delete user');
