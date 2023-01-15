<?php

use App\Http\Controllers\Api\Auth\LoginController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// OpenApi Json Resource
Route::get('/resource.json', [\App\Http\Controllers\Api\Doc\DocGeneratorController::class, 'render']);

// Lead
Route::post('lead', [\App\Http\Controllers\Api\Lead\LeadCreateController::class, 'create'])->middleware(['auth:sanctum']);
Route::get('lead/{id}', [\App\Http\Controllers\Api\Lead\LeadReadController::class, 'read'])->middleware(['auth:sanctum']);

// Customer
//Route::get('/customers', 'Api\Customer\CustomerListController@index');

// Product
//Route::get('/products', 'Api\Product\ProductListController@index');

// Contact
Route::post('/contact', [\App\Http\Controllers\Api\Contact\ContactCreateController::class, 'create']);
//Route::get('/contact', 'Api\Contact\ContactListController@index');
//Route::patch('/contact/{id}', 'Api\Contact\ContactUpdateController@update');

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('login', [LoginController::class, 'login']);
    Route::post('logout', [LoginController::class, 'logout']);
    Route::post('refresh', [LoginController::class, 'refresh']);
    Route::post('me', [LoginController::class, 'me']);
});
