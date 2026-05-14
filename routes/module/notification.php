<?php

declare(strict_types=1);

use App\Http\Controllers\Notification\DeleteNotificationController;
use App\Http\Controllers\Notification\GetLatestAjaxController;
use App\Http\Controllers\Notification\NotificationIndexController;
use App\Http\Controllers\Notification\SetNotificationReadAjaxController;
use Illuminate\Support\Facades\Route;

Route::get('/notification', [NotificationIndexController::class, 'index']);
Route::get('/ajax/notification', [GetLatestAjaxController::class, 'getLatest'])
    ->middleware('auth');
Route::get('/notification/read/{id}',
    [SetNotificationReadAjaxController::class, 'setRead'])
    ->middleware('auth');
Route::get('/notification/delete/{id}',
    [DeleteNotificationController::class, 'delete']);
