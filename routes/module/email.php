<?php

declare(strict_types=1);

use App\Http\Controllers\Email\EmailCreateController;
use App\Http\Controllers\Email\EmailDeleteController;
use App\Http\Controllers\Email\EmailDownloadAttachmentController;
use App\Http\Controllers\Email\EmailDuplicateController;
use App\Http\Controllers\Email\EmailIndexController;
use App\Http\Controllers\Email\EmailSaveController;
use App\Http\Controllers\Email\EmailSendController;
use App\Http\Controllers\Email\EmailTrackingController;
use App\Http\Controllers\Email\EmailUpdateController;
use App\Http\Controllers\Email\EmailViewController;
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/email', [EmailIndexController::class, 'index']);
Route::get('/email/create', [EmailCreateController::class, 'create']);
Route::get('/email/view/{id}', [EmailViewController::class, 'view']);
Route::get('/email/update/{id}', [EmailUpdateController::class, 'update']);
Route::post('/email/save', [EmailSaveController::class, 'save']);
Route::get('/email/send/{id}', [EmailSendController::class, 'send']);
Route::get('/email/delete/{id}', [EmailDeleteController::class, 'delete']);
Route::match(['get', 'post'], '/email/duplicate',
    [EmailDuplicateController::class, 'duplicate'])
    ->name('email.duplicate');
Route::get('/email/download-attachment/{attachmentId}',
    [EmailDownloadAttachmentController::class, 'downloadAttachment'])
    ->name('downloadAttachment');
Route::get('/email/tracking/{uuid}', [EmailTrackingController::class, 'track_email']);
