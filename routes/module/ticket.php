<?php

declare(strict_types=1);

use App\Http\Controllers\Ticket\TicketCreateController;
use App\Http\Controllers\Ticket\TicketCreateMessageController;
use App\Http\Controllers\Ticket\TicketDeleteController;
use App\Http\Controllers\Ticket\TicketIndexController;
use App\Http\Controllers\Ticket\TicketSaveController;
use App\Http\Controllers\Ticket\TicketUpdateController;
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/ticket', [TicketIndexController::class, 'index']);
Route::get('/ticket/create', [TicketCreateController::class, 'create']);
Route::get('/ticket/update/{id}', [TicketUpdateController::class, 'update']);
Route::post('/ticket/save', [TicketSaveController::class, 'save']);
Route::get('/ticket/delete/{id}', [TicketDeleteController::class, 'delete']);
Route::post('/ticket/message/save', [TicketCreateMessageController::class, 'save']);
