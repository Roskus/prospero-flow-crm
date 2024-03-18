<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Calendar\CalendarExportController;

Route::match(['get', 'post'], '/calendar/{date?}',
    [\App\Http\Controllers\Calendar\CalendarIndexController::class, 'index'])->name('calendar.index');
Route::post('/calendar/event/save', [\App\Http\Controllers\Calendar\CalendarSaveEventController::class, 'save'])
    ->name('calendar.save');
Route::get('/calendar/event/update/{id}',
    [\App\Http\Controllers\Calendar\CalendarUpdateEventController::class, 'update'])->name('calendar.update');
Route::get('/calendar/event/delete/{id}',
    [\App\Http\Controllers\Calendar\CalendarDeleteEventController::class, 'delete'])->name('calendar.delete');
Route::get('/calendar/{id}/export', [CalendarExportController::class, 'exportICal'])
    ->name('calendar.export');
