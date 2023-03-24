<?php

declare(strict_types=1);

use App\Http\Controllers\Lead\LeadCreateController;
use App\Http\Controllers\Lead\LeadDeleteController;
use App\Http\Controllers\Lead\LeadExportController;
use App\Http\Controllers\Lead\LeadImportIndexController;
use App\Http\Controllers\Lead\LeadImportSaveController;
use App\Http\Controllers\Lead\LeadIndexController;
use App\Http\Controllers\Lead\LeadPromoteCustomerController;
use App\Http\Controllers\Lead\LeadSaveController;
use App\Http\Controllers\Lead\LeadShowController;
use App\Http\Controllers\Lead\LeadUpdateController;
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/lead',
    [LeadIndexController::class, 'index'])
    ->can('read lead');

Route::get('/lead/create',
    [LeadCreateController::class, 'create'])
    ->can('create lead');

Route::get('/lead/update/{id}',
    [LeadUpdateController::class, 'update'])
    ->can('update lead');

Route::get('/lead/promote/{id}',
    [LeadPromoteCustomerController::class, 'promote']);

Route::post('/lead/save',
    [LeadSaveController::class, 'save'])
    ->can('create lead')->can('update lead');

Route::get('/lead/import',
    [LeadImportIndexController::class, 'index'])
    ->can('create lead');

Route::post('/lead/import/save',
    [LeadImportSaveController::class, 'save'])
    ->can('create lead');

Route::get('/lead/delete/{id}',
    [LeadDeleteController::class, 'delete'])
    ->can('delete lead');

Route::get('/lead/export',
    [LeadExportController::class, 'export'])
    ->can('export lead');

Route::get('/lead/show/{lead}',
    [LeadShowController::class, 'show'])
    ->can('read lead');
