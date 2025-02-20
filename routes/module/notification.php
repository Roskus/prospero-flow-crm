<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/notification', [\App\Http\Controllers\Notification\NotificationIndexController::class, 'index']);
Route::get('/ajax/notification', [\App\Http\Controllers\Notification\GetLatestAjaxController::class, 'getLatest'])
    ->middleware('auth');
Route::get('/notification/read/{id}',
    [\App\Http\Controllers\Notification\SetNotificationReadAjaxController::class, 'setRead'])
    ->middleware('auth');
Route::get('/notification/delete/{id}',
    [\App\Http\Controllers\Notification\DeleteNotificationController::class, 'delete']);
