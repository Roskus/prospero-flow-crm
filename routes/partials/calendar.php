<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/calendar/{date?}',
    [\App\Http\Controllers\Calendar\CalendarController::class, 'index'])->name('calendar.index');
Route::post('/calendar/event/save', [\App\Http\Controllers\Calendar\SaveCalendarEventController::class, 'save'])
    ->name('calendar.save');
Route::get('/calendar/event/update/{id}',
    [\App\Http\Controllers\Calendar\UpdateCalendarEventController::class, 'update'])->name('calendar.update');
Route::delete('/calendar/event/delete/{id}',
    [\App\Http\Controllers\Calendar\DeleteCalendarEventController::class, 'delete'])->name('calendar.delete');
