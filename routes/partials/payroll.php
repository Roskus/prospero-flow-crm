<?php

use Illuminate\Support\Facades\Route;

// Rutas para el módulo de Payroll
Route::match(['get', 'post'], '/payroll', [\App\Http\Controllers\Payroll\PayrollIndexController::class, 'index']);
Route::get('/payroll/create', [\App\Http\Controllers\Payroll\PayrollCreateController::class, 'create']);
Route::get('/payroll/show/{id}', [\App\Http\Controllers\Payroll\PayrollShowController::class, 'show']);
Route::post('/payroll/update/{id}', [\App\Http\Controllers\Payroll\PayrollUpdateController::class, 'update']);
Route::get('/payroll/delete/{id}', [\App\Http\Controllers\Payroll\PayrollDeleteController::class, 'delete']);
Route::post('/payroll/save', [\App\Http\Controllers\Payroll\PayrollSaveController::class, 'save']);
