<?php

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// OpenApi Json Resource
Route::get('/resource.json', [\App\Http\Controllers\Api\Doc\DocGeneratorController::class, 'render']);

// Customer
//Route::get('/customers', 'Api\Customer\CustomerListController@index');

// Product
//Route::get('/products', 'Api\Product\ProductListController@index');

// Contact
Route::post('/contact', [\App\Http\Controllers\Api\Contact\ContactCreateController::class, 'create']);
//Route::get('/contact', 'Api\Contact\ContactListController@index');
//Route::patch('/contact/{id}', 'Api\Contact\ContactUpdateController@update');
