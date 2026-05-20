<?php

declare(strict_types=1);

use App\Http\Controllers\Supplier\Contact\ContactCreateController;
use App\Http\Controllers\Supplier\Contact\ContactDeleteController;
use App\Http\Controllers\Supplier\Contact\ContactExportVCard;
use App\Http\Controllers\Supplier\Contact\ContactSaveController;
use App\Http\Controllers\Supplier\Contact\ContactUpdateController;
use App\Http\Controllers\Supplier\SupplierCreateController;
use App\Http\Controllers\Supplier\SupplierDeleteController;
use App\Http\Controllers\Supplier\SupplierIndexController;
use App\Http\Controllers\Supplier\SupplierSaveController;
use App\Http\Controllers\Supplier\SupplierUpdateController;
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/supplier', [SupplierIndexController::class, 'index']);
Route::get('/supplier/create', [SupplierCreateController::class, 'create']);
Route::get('/supplier/update/{id}', [SupplierUpdateController::class, 'update']);
Route::post('/supplier/save', [SupplierSaveController::class, 'save']);
Route::get('/supplier/delete/{id}', [SupplierDeleteController::class, 'delete']);

Route::post('/supplier/contact/save', [ContactSaveController::class, 'save']);
Route::get('/supplier/contact/export-vcard/{id}', [ContactExportVCard::class, 'export']);
Route::get('/supplier/contact/create/{model}/{id_model}', [ContactCreateController::class, 'create']);
Route::get('/supplier/contact/update/{id}', [ContactUpdateController::class, 'update']);
Route::get('/supplier/contact/delete/{id}', [ContactDeleteController::class, 'delete']);
