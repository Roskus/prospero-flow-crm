<?php

declare(strict_types=1);

use App\Http\Controllers\Asset\AssetAssignController;
use App\Http\Controllers\Asset\AssetCodeController;
use App\Http\Controllers\Asset\AssetCodeNextController;
use App\Http\Controllers\Asset\AssetCreateController;
use App\Http\Controllers\Asset\AssetDeleteController;
use App\Http\Controllers\Asset\AssetIndexController;
use App\Http\Controllers\Asset\AssetLabelController;
use App\Http\Controllers\Asset\AssetPhotoController;
use App\Http\Controllers\Asset\AssetReturnController;
use App\Http\Controllers\Asset\AssetSaveController;
use App\Http\Controllers\Asset\AssetShowController;
use App\Http\Controllers\Asset\AssetUpdateController;
use App\Http\Controllers\Asset\Assignment\AssetAssignmentSaveController;
use App\Http\Controllers\Asset\Assignment\AssetAssignmentUpdateController;
use App\Http\Controllers\Asset\Maintenance\AssetMaintenanceCreateController;
use App\Http\Controllers\Asset\Maintenance\AssetMaintenanceDeleteController;
use App\Http\Controllers\Asset\Maintenance\AssetMaintenanceSaveController;
use App\Http\Controllers\Asset\Maintenance\AssetMaintenanceUpdateController;
use Illuminate\Support\Facades\Route;

Route::get('/inventory/{category}', [AssetIndexController::class, 'index'])->whereIn('category', ['vehicle', 'device'])->name('asset.index')->can('read asset');
Route::get('/inventory/{category}/create', [AssetCreateController::class, 'create'])->whereIn('category', ['vehicle', 'device'])->can('create asset');
Route::get('/inventory/show/{id}', [AssetShowController::class, 'show'])->whereNumber('id')->name('asset.show')->can('read asset');
Route::get('/inventory/update/{id}', [AssetUpdateController::class, 'update'])->whereNumber('id')->can('update asset');
Route::post('/inventory/save', [AssetSaveController::class, 'save'])->middleware('permission:create asset|update asset');
Route::delete('/inventory/delete/{id}', [AssetDeleteController::class, 'delete'])->whereNumber('id')->can('delete asset');
Route::get('/inventory/code/next', [AssetCodeNextController::class, 'next'])->middleware('permission:create asset|update asset');
Route::get('/inventory/code/{code}', [AssetCodeController::class, 'show'])->where('code', '[A-Za-z0-9-]+')->name('asset.code')->can('read asset');
Route::get('/inventory/label/{id}', [AssetLabelController::class, 'show'])->whereNumber('id')->name('asset.label')->can('read asset');
Route::get('/inventory/photo/{id}', [AssetPhotoController::class, 'show'])->whereNumber('id')->name('asset.photo')->can('read asset');

Route::post('/inventory/assign/{id}', [AssetAssignController::class, 'assign'])->whereNumber('id')->can('update asset');
Route::post('/inventory/return/{id}', [AssetReturnController::class, 'return'])->whereNumber('id')->can('update asset');
Route::get('/inventory/assignment/update/{id}', [AssetAssignmentUpdateController::class, 'update'])->whereNumber('id')->can('update asset');
Route::post('/inventory/assignment/save', [AssetAssignmentSaveController::class, 'save'])->can('update asset');

Route::get('/inventory/maintenance/create/{assetId}', [AssetMaintenanceCreateController::class, 'create'])->whereNumber('assetId')->can('update asset');
Route::get('/inventory/maintenance/update/{id}', [AssetMaintenanceUpdateController::class, 'update'])->whereNumber('id')->can('update asset');
Route::post('/inventory/maintenance/save', [AssetMaintenanceSaveController::class, 'save'])->can('update asset');
Route::delete('/inventory/maintenance/delete/{id}', [AssetMaintenanceDeleteController::class, 'delete'])->whereNumber('id')->can('update asset');
