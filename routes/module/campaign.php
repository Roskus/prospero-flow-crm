<?php

declare(strict_types=1);

use App\Http\Controllers\Campaign\CampaignCreateController;
use App\Http\Controllers\Campaign\CampaignIndexController;
use App\Http\Controllers\Campaign\CampaignSaveController;
use App\Http\Controllers\Campaign\CampaignUpdateController;
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/campaign', [CampaignIndexController::class, 'index']);
Route::get('/campaign/create', [CampaignCreateController::class, 'create']);
Route::get('/campaign/update/{id}', [CampaignUpdateController::class, 'update']);
Route::post('/campaign/save', [CampaignSaveController::class, 'save']);
