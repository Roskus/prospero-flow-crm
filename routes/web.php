<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/','MainController@index');

//Order
Route::get('/order','OrderController@index');
Route::get('/order/add','OrderController@add');
Route::get('/order/edit/{id}','OrderController@edit',function (Request $request,$id) {});
//Product
Route::get('/product','ProductController@index');
Route::get('/product/add','ProductController@add');
Route::get('/product/edit/{id}','ProductController@edit',function (Request $request,$id) {});
Route::post('/product/save','ProductController@save',function (Request $request) {});
