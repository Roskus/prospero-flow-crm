<?php

use Illuminate\Support\Facades\Route;

Route::get('/company', [\App\Http\Controllers\Company\CompanyIndexController::class, 'index']);
Route::get('/company/create', [\App\Http\Controllers\Company\CompanyCreateController::class, 'create']);
Route::get('/company/update/{id}', [\App\Http\Controllers\Company\CompanyUpdateController::class, 'update']);
Route::post('/company/save', [\App\Http\Controllers\Company\CompanySaveController::class, 'save']);
Route::get('/company/delete/{id}', [\App\Http\Controllers\Company\CompanyDeleteController::class, 'delete']);
