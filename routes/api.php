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
Route::get('/resource.json', 'Api\Customer\DocGeneratoController@render');

// Customer
Route::get('/customers', 'Api\Customer\CustomerListController@index');

// Product
Route::get('/products', 'Api\Product\ProductListController@index');
