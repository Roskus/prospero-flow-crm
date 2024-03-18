<?php

use Illuminate\Support\Facades\Route;

Route::post('/contact/save', [\App\Http\Controllers\Contact\ContactSaveController::class, 'save']);
Route::get('/contact/search/{name}', [\App\Http\Controllers\Contact\ContactGetAjaxController::class, 'searchByName']);
Route::get('/contact/export-vcard/{id}', [\App\Http\Controllers\Contact\ContactExportVCard::class, 'export']);
Route::get('/contact/create/{model}/{id_model}', [\App\Http\Controllers\Contact\ContactCreateController::class, 'create']);
Route::get('/contact/update/{id}', [\App\Http\Controllers\Contact\ContactUpdateController::class, 'update']);
Route::get('/contact/delete/{id}', [\App\Http\Controllers\Contact\ContactDeleteController::class, 'delete']);
