<?php

declare(strict_types=1);

use App\Http\Controllers\Carrier\CarrierCreateController;
use App\Http\Controllers\Carrier\CarrierDeleteController;
use App\Http\Controllers\Carrier\CarrierIndexController;
use App\Http\Controllers\Carrier\CarrierSaveController;
use App\Http\Controllers\Carrier\CarrierUpdateController;
use Illuminate\Support\Facades\Route;

Route::get('/carrier', [CarrierIndexController::class, 'index'])->name('carrier.index')->can('read asset');
Route::get('/carrier/create', [CarrierCreateController::class, 'create'])->can('update asset');
Route::get('/carrier/update/{id}', [CarrierUpdateController::class, 'update'])->can('update asset');
Route::post('/carrier/save', [CarrierSaveController::class, 'save'])->can('update asset');
Route::delete('/carrier/delete/{id}', [CarrierDeleteController::class, 'delete'])->can('delete asset');
