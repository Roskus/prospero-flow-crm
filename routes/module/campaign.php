<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/campaign', [\App\Http\Controllers\Campaign\CampaignIndexController::class, 'index']);
Route::get('/campaign/create', [\App\Http\Controllers\Campaign\CampaignCreateController::class, 'create']);
Route::get('/campaign/update/{id}', [\App\Http\Controllers\Campaign\CampaignUpdateController::class, 'update']);
Route::post('/campaign/save', [\App\Http\Controllers\Campaign\CampaignSaveController::class, 'save']);
