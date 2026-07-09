<?php

declare(strict_types=1);

use App\Http\Controllers\Payroll\PayrollCreateController;
use App\Http\Controllers\Payroll\PayrollDeleteController;
use App\Http\Controllers\Payroll\PayrollIndexController;
use App\Http\Controllers\Payroll\PayrollSaveController;
use App\Http\Controllers\Payroll\PayrollShowController;
use App\Http\Controllers\Payroll\PayrollUpdateController;
use Illuminate\Support\Facades\Route;

// Rutas para el módulo de Payroll
Route::match(['get', 'post'], '/payroll', [PayrollIndexController::class, 'index']);
Route::get('/payroll/create', [PayrollCreateController::class, 'create']);
Route::get('/payroll/show/{id}', [PayrollShowController::class, 'show']);
Route::match(['get', 'post'], '/payroll/update/{id}', [PayrollUpdateController::class, 'update']);
Route::get('/payroll/delete/{id}', [PayrollDeleteController::class, 'delete']);
Route::post('/payroll/save', [PayrollSaveController::class, 'save']);
