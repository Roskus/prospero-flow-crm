<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/supplier', [\App\Http\Controllers\Supplier\SupplierIndexController::class, 'index']);
Route::get('/supplier/create', [\App\Http\Controllers\Supplier\SupplierCreateController::class, 'create']);
Route::get('/supplier/update/{id}', [\App\Http\Controllers\Supplier\SupplierUpdateController::class, 'update']);
Route::post('/supplier/save', [\App\Http\Controllers\Supplier\SupplierSaveController::class, 'save']);

Route::post('/supplier/contact/save', [\App\Http\Controllers\Supplier\Contact\ContactSaveController::class, 'save']);
Route::get('/supplier/contact/export-vcard/{id}', [\App\Http\Controllers\Supplier\Contact\ContactExportVCard::class, 'export']);
Route::get('/supplier/contact/create/{model}/{id_model}', [\App\Http\Controllers\Supplier\Contact\ContactCreateController::class, 'create']);
Route::get('/supplier/contact/update/{id}', [\App\Http\Controllers\Supplier\Contact\ContactUpdateController::class, 'update']);
Route::get('/supplier/contact/delete/{id}', [\App\Http\Controllers\Supplier\Contact\ContactDeleteController::class, 'delete']);
