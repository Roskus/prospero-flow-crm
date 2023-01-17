<?php

use App\Http\Controllers\Api\Auth\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/** PROTECTED ROUTES */
Route::middleware(['api'])->group(function () {
    // Lead
    Route::post('lead', [\App\Http\Controllers\Api\Lead\LeadCreateController::class, 'create'])->middleware(['auth:api']);
    Route::get('lead/{id}', [\App\Http\Controllers\Api\Lead\LeadReadController::class, 'read'])->middleware(['auth:api']);
    Route::get('lead', [\App\Http\Controllers\Api\Lead\LeadListController::class, 'index'])->middleware(['auth:api']);

    // Customer
    //Route::get('/customers', 'Api\Customer\CustomerListController@index');

    // Product
    //Route::get('/products', 'Api\Product\ProductListController@index');

    // Contact
    Route::post('/contact', [\App\Http\Controllers\Api\Contact\ContactCreateController::class, 'create'])->middleware(['auth:api']);
    //Route::get('/contact', 'Api\Contact\ContactListController@index');
    //Route::patch('/contact/{id}', 'Api\Contact\ContactUpdateController@update');

    /** AUTH ROUTES */
    Route::prefix('auth')->group(function () {
        Route::post('logout', [LoginController::class, 'logout']);
        Route::post('refresh', [LoginController::class, 'refresh']);
        Route::post('me', [LoginController::class, 'me']);
    });
});

/** UNPROTECTED ROUTES */

// Login
Route::post('/auth/login', [LoginController::class, 'login']);

// OpenApi Json Resource
Route::get('/resource.json', [\App\Http\Controllers\Api\Doc\DocGeneratorController::class, 'render']);
