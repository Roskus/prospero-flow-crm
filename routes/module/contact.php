<?php

declare(strict_types=1);

use App\Http\Controllers\Contact\ContactCreateController;
use App\Http\Controllers\Contact\ContactDeleteController;
use App\Http\Controllers\Contact\ContactExportVCard;
use App\Http\Controllers\Contact\ContactGetAjaxController;
use App\Http\Controllers\Contact\ContactSaveController;
use App\Http\Controllers\Contact\ContactUpdateController;
use Illuminate\Support\Facades\Route;

Route::post('/contact/save', [ContactSaveController::class, 'save']);
Route::get('/contact/search/{name}', [ContactGetAjaxController::class, 'searchByName']);
Route::get('/contact/export-vcard/{id}', [ContactExportVCard::class, 'export']);
Route::get('/contact/create/{model}/{id_model}', [ContactCreateController::class, 'create']);
Route::get('/contact/update/{id}', [ContactUpdateController::class, 'update']);
Route::get('/contact/delete/{id}', [ContactDeleteController::class, 'delete']);
