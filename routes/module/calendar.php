<?php

declare(strict_types=1);

use App\Http\Controllers\Calendar\CalendarDeleteEventController;
use App\Http\Controllers\Calendar\CalendarExportController;
use App\Http\Controllers\Calendar\CalendarIndexController;
use App\Http\Controllers\Calendar\CalendarSaveEventController;
use App\Http\Controllers\Calendar\CalendarUpdateEventController;
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/calendar/{date?}',
    [CalendarIndexController::class, 'index'])->name('calendar.index')->can('read calendar');
Route::post('/calendar/event/save', [CalendarSaveEventController::class, 'save'])
    ->name('calendar.save')->can('create calendar');
Route::get('/calendar/event/update/{id}',
    [CalendarUpdateEventController::class, 'update'])->name('calendar.update')->can('update calendar');
Route::get('/calendar/event/delete/{id}',
    [CalendarDeleteEventController::class, 'delete'])->name('calendar.delete')->can('delete calendar');
Route::get('/calendar/{id}/export', [CalendarExportController::class, 'exportICal'])
    ->name('calendar.export')->can('read calendar');
