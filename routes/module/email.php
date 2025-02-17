<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/email', [\App\Http\Controllers\Email\EmailIndexController::class, 'index']);
Route::get('/email/create', [\App\Http\Controllers\Email\EmailCreateController::class, 'create']);
Route::get('/email/view/{id}', [\App\Http\Controllers\Email\EmailViewController::class, 'view']);
Route::get('/email/update/{id}', [\App\Http\Controllers\Email\EmailUpdateController::class, 'update']);
Route::post('/email/save', [\App\Http\Controllers\Email\EmailSaveController::class, 'save']);
Route::get('/email/send/{id}', [\App\Http\Controllers\Email\EmailSendController::class, 'send']);
Route::get('/email/delete/{id}', [\App\Http\Controllers\Email\EmailDeleteController::class, 'delete']);
Route::match(['get', 'post'], '/email/duplicate',
    [\App\Http\Controllers\Email\EmailDuplicateController::class, 'duplicate'])
    ->name('email.duplicate');
Route::get('/email/download-attachment/{attachmentId}',
    [\App\Http\Controllers\Email\EmailDownloadAttachmentController::class, 'downloadAttachment'])
    ->name('downloadAttachment');
Route::get('/email/tracking/{uuid}', [\App\Http\Controllers\Email\EmailTrackingController::class, 'track_email']);
