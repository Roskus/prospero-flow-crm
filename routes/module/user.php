<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/user', [\App\Http\Controllers\User\UserListController::class, 'index']);
Route::get('/user/create', [\App\Http\Controllers\User\UserCreateController::class, 'create']);
Route::get('/user/update/{id}', [\App\Http\Controllers\User\UserUpdateController::class, 'update']);
Route::post('/user/save', [\App\Http\Controllers\User\UserSaveController::class, 'save']);
Route::get('/user/delete/{id}', [\App\Http\Controllers\User\UserDeleteController::class, 'delete']);
