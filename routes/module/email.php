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

Route::match(['get', 'post'], '/email', [EmailIndexController::class, 'index'])->can('read email');
Route::get('/email/create', [EmailCreateController::class, 'create'])->can('create email');
Route::get('/email/view/{id}', [EmailViewController::class, 'view'])->can('read email');
Route::get('/email/update/{id}', [EmailUpdateController::class, 'update'])->can('update email');
Route::post('/email/save', [EmailSaveController::class, 'save'])->can('create email')->can('update email');
Route::get('/email/send/{id}', [EmailSendController::class, 'send'])->can('update email');
Route::get('/email/delete/{id}', [EmailDeleteController::class, 'delete'])->can('delete email');
Route::match(['get', 'post'], '/email/duplicate',
    [EmailDuplicateController::class, 'duplicate'])
    ->name('email.duplicate')->can('create email');
Route::get('/email/download-attachment/{attachmentId}',
    [EmailDownloadAttachmentController::class, 'downloadAttachment'])
    ->name('downloadAttachment')->can('read email');
Route::get('/email/tracking/{uuid}', [EmailTrackingController::class, 'track_email']);
