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

Route::match(['get', 'post'], '/supplier', [SupplierIndexController::class, 'index'])->name('supplier.index')->can('read supplier');
Route::get('/supplier/create', [SupplierCreateController::class, 'create'])->can('create supplier');
Route::get('/supplier/update/{id}', [SupplierUpdateController::class, 'update'])->can('update supplier');
Route::post('/supplier/save', [SupplierSaveController::class, 'save'])->can('create supplier')->can('update supplier');
Route::get('/supplier/delete/{id}', [SupplierDeleteController::class, 'delete'])->can('delete supplier');

Route::post('/supplier/contact/save', [ContactSaveController::class, 'save'])->can('update supplier');
Route::get('/supplier/contact/export-vcard/{id}', [ContactExportVCard::class, 'export'])->can('read supplier');
Route::get('/supplier/contact/create/{model}/{id_model}', [ContactCreateController::class, 'create'])->can('update supplier');
Route::get('/supplier/contact/update/{id}', [ContactUpdateController::class, 'update'])->can('update supplier');
Route::get('/supplier/contact/delete/{id}', [ContactDeleteController::class, 'delete'])->can('update supplier');
