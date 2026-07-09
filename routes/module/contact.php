<?php

declare(strict_types=1);

use App\Http\Controllers\Contact\ContactCreateController;
use App\Http\Controllers\Contact\ContactDeleteController;
use App\Http\Controllers\Contact\ContactExportVCard;
use App\Http\Controllers\Contact\ContactGetAjaxController;
use App\Http\Controllers\Contact\ContactSaveController;
use App\Http\Controllers\Contact\ContactUpdateController;
use Illuminate\Support\Facades\Route;

Route::post('/contact/save', [ContactSaveController::class, 'save'])->can('create contact')->can('update contact');
Route::get('/contact/search/{name}', [ContactGetAjaxController::class, 'searchByName'])->can('read contact');
Route::get('/contact/export-vcard/{id}', [ContactExportVCard::class, 'export'])->can('read contact');
Route::get('/contact/create/{model}/{id_model}', [ContactCreateController::class, 'create'])->can('create contact');
Route::get('/contact/update/{id}', [ContactUpdateController::class, 'update'])->can('update contact');
Route::get('/contact/delete/{id}', [ContactDeleteController::class, 'delete'])->can('delete contact');
