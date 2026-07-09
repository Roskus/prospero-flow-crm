<?php

declare(strict_types=1);

use App\Http\Controllers\Campaign\CampaignCreateController;
use App\Http\Controllers\Campaign\CampaignIndexController;
use App\Http\Controllers\Campaign\CampaignSaveController;
use App\Http\Controllers\Campaign\CampaignUpdateController;
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/campaign', [CampaignIndexController::class, 'index'])->can('read campaign');
Route::get('/campaign/create', [CampaignCreateController::class, 'create'])->can('create campaign');
Route::get('/campaign/update/{id}', [CampaignUpdateController::class, 'update'])->can('update campaign');
Route::post('/campaign/save', [CampaignSaveController::class, 'save'])->can('create campaign')->can('update campaign');
