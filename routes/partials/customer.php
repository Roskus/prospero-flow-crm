<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\CustomerCreateController;
use App\Http\Controllers\Customer\CustomerDeleteController;
use App\Http\Controllers\Customer\CustomerExportController;
use App\Http\Controllers\Customer\CustomerImportIndexController;
use App\Http\Controllers\Customer\CustomerImportSaveController;
use App\Http\Controllers\Customer\CustomerIndexController;
use App\Http\Controllers\Customer\CustomerSaveController;
use App\Http\Controllers\Customer\CustomerShowController;
use App\Http\Controllers\Customer\CustomerUpdateController;
use App\Http\Controllers\Customer\CustomerCreateMessageController;

Route::match(['get', 'post'], '/customer',
    [CustomerIndexController::class, 'index'])
    ->can('read customer');

Route::get('/customer/create',
    [CustomerCreateController::class, 'create'])
    ->can('create customer');

Route::get('/customer/update/{id}',
    [CustomerUpdateController::class, 'update'])
    ->can('update customer');

Route::post('/customer/save',
    [CustomerSaveController::class, 'save'])
    ->can('create customer')->can('update customer');

Route::get('/customer/import',
    [CustomerImportIndexController::class, 'index']);

Route::post('/customer/import/save',
    [CustomerImportSaveController::class, 'save']);

Route::get('/customer/delete/{id}',
    [CustomerDeleteController::class, 'delete'])
    ->can('delete customer');

Route::get('/customer/export',
    [CustomerExportController::class, 'export'])
    ->can('export customer');

Route::get('/customer/show/{customer}',
    [CustomerShowController::class, 'show'])
    ->can('read customer');

Route::post('/customer/message/save', [CustomerCreateMessageController::class, 'save']);
