<?php

declare(strict_types=1);

use App\Http\Controllers\Company\CompanyCreateController;
use App\Http\Controllers\Company\CompanyDeleteController;
use App\Http\Controllers\Company\CompanyIndexController;
use App\Http\Controllers\Company\CompanySaveController;
use App\Http\Controllers\Company\CompanyUpdateController;
use Illuminate\Support\Facades\Route;

Route::get('/company', [CompanyIndexController::class, 'index'])->name('company.index')->can('read company');
Route::get('/company/create', [CompanyCreateController::class, 'create'])->can('create company');
Route::get('/company/update/{id}', [CompanyUpdateController::class, 'update'])->can('update company');
Route::post('/company/save', [CompanySaveController::class, 'save'])->can('create company')->can('update company');
Route::delete('/company/delete/{id}', [CompanyDeleteController::class, 'delete'])->can('delete company');
