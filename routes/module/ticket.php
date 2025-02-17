<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/ticket', [\App\Http\Controllers\Ticket\TicketIndexController::class, 'index']);
Route::get('/ticket/create', [\App\Http\Controllers\Ticket\TicketCreateController::class, 'create']);
Route::get('/ticket/update/{id}', [\App\Http\Controllers\Ticket\TicketUpdateController::class, 'update']);
Route::post('/ticket/save', [\App\Http\Controllers\Ticket\TicketSaveController::class, 'save']);
Route::get('/ticket/delete/{id}', [\App\Http\Controllers\Ticket\TicketDeleteController::class, 'delete']);
Route::post('/ticket/message/save', [\App\Http\Controllers\Ticket\TicketCreateMessageController::class, 'save']);
