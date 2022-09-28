<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/', 'MainController@index');

Auth::routes(['register' => (env('APP_ENV') != 'production') ? true : false]);

//Order
Route::get('/order', 'OrderController@index');
Route::get('/order/add', 'OrderController@add');
Route::get('/order/edit/{id}', 'OrderController@edit', function (Request $request, int $id) {
});
Route::post('/order/save', 'OrderController@save', function (Request $request) {
});

//Product
Route::match(['get', 'post'], '/product', 'ProductController@index');
Route::get('/product/create', [\App\Http\Controllers\Product\ProductCreateController::class, 'create']);
Route::get('/product/update/{id}', [\App\Http\Controllers\Product\ProductUpdateController::class, 'update'], function (Request $request, int $id) {
});
Route::post('/product/save', [\App\Http\Controllers\Product\ProductSaveController::class, 'save'], function (Request $request) {
});

//Brand
Route::get('/brand', 'BrandController@index');
Route::get('/brand/add', 'BrandController@add');
Route::get('/brand/edit/{id}', 'BrandController@edit', function (Request $request, int $id) {
});
Route::post('/brand/save', 'BrandController@save', function (Request $request) {
});

// Lead
Route::match(['get', 'post'], '/lead', [\App\Http\Controllers\Lead\LeadIndexController::class, 'index']);
Route::get('/lead/create', [\App\Http\Controllers\Lead\LeadCreateController::class, 'create']);
Route::get('/lead/update/{id}', [\App\Http\Controllers\Lead\LeadUpdateController::class, 'update'], function (Request $request, int $id) {
});
Route::post('/lead/save', [\App\Http\Controllers\Lead\LeadSaveController::class, 'save'], function (Request $request) {
});
Route::get('/lead/import', [\App\Http\Controllers\Lead\LeadImportIndexController::class, 'index']);
Route::post('/lead/import/save', [\App\Http\Controllers\Lead\LeadImportSaveController::class, 'save'], function (Request $request) {
});
Route::get('/lead/delete/{id}', [\App\Http\Controllers\Lead\LeadDeleteController::class, 'delete'], function (Request $request, int $id) {
});

//Customer
Route::match(['get', 'post'], '/customer', [\App\Http\Controllers\Customer\CustomerIndexController::class, 'index']);
Route::get('/customer/create', [\App\Http\Controllers\Customer\CustomerCreateController::class, 'create']);
Route::get('/customer/update/{id}', [\App\Http\Controllers\Customer\CustomerUpdateController::class, 'update'], function (Request $request, $id) {
});
Route::post('/customer/save', [\App\Http\Controllers\Customer\CustomerSaveController::class, 'save'], function (Request $request) {
});

//Category
Route::get('/category', 'CategoryController@index');
Route::get('/category/add', 'CategoryController@add');
Route::get('/category/edit/{id}', 'CategoryController@edit', function (Request $request, $id) {
});
Route::post('/category/save', 'CategoryController@save', function (Request $request) {
});

// Company
Route::get('/company', [\App\Http\Controllers\Company\CompanyIndexController::class, 'index'], function (Request $request) {
});
Route::get('/company/add', 'CompanyController@add');
Route::get('/company/edit/{id}', 'CompanyController@edit', function (Request $request, int $id) {
});
Route::post('/company/save', 'CompanyController@save', function (Request $request) {
});
Route::get('/company/delete/{id}', [\App\Http\Controllers\Company\CompanyDeleteController::class, 'delete'], function (Request $request, int $id) {
});

// Account
Route::get('/accounting', 'AccountController@index');
Route::post('/account/save', 'AccountController@save');

// User
Route::get('/user', [\App\Http\Controllers\User\UserListController::class, 'index'], function (Request $request) {
});
Route::get('/user/add', 'UserController@add');
Route::get('/user/edit/{id}', 'UserController@edit', function (Request $request, int $id) {
});
Route::post('/user/save', [\App\Http\Controllers\User\UserSaveController::class, 'save'], function (Request $request) {
});

// Profile
Route::get('/profile', 'ProfileController@edit');
Route::post('/profile/save', [\App\Http\Controllers\Profile\ProfileSaveController::class, 'save'], function (Request $request) {
});

Route::get('/setting', 'SettingController@index');
Route::get('/home', 'HomeController@index');

// Calendar
Route::match(['get', 'post'], '/calendar', [\App\Http\Controllers\Calendar\CalendarController::class, 'index'], function (Request $request) {
});

// Email
Route::match(['get', 'post'], '/email', [\App\Http\Controllers\Email\EmailIndexController::class, 'index'], function (Request $request) {
});
Route::get('/email/create', [\App\Http\Controllers\Email\EmailCreateController::class, 'create'], function (Request $request) {
});
Route::get('/email/update/{id}', [\App\Http\Controllers\Email\EmailUpdateController::class, 'update'], function (Request $request, int $id) {
});
Route::post('/email/save', [\App\Http\Controllers\Email\EmailSaveController::class, 'save'], function (Request $request) {
});
Route::get('/email/send/{id}', [\App\Http\Controllers\Email\EmailSendController::class, 'send'], function (Request $request, int $id) {
});

// Supplier
Route::match(['get', 'post'], '/supplier', [\App\Http\Controllers\Supplier\SupplierIndexController::class, 'index'], function (Request $request) {
});
Route::get('/supplier/create', [\App\Http\Controllers\Supplier\SupplierCreateController::class, 'create'], function (Request $request) {
});
Route::get('/supplier/update/{id}', [\App\Http\Controllers\Supplier\SupplierUpdateController::class, 'update'], function (Request $request, int $id) {
});
Route::post('/supplier/save', [\App\Http\Controllers\Supplier\SupplierSaveController::class, 'save'], function (Request $request) {
});

// Ticket
Route::match(['get', 'post'], '/ticket', [\App\Http\Controllers\Ticket\TicketIndexController::class, 'index'], function (Request $request) {
});
Route::get('/ticket/create', [\App\Http\Controllers\Ticket\TicketCreateController::class, 'create'], function (Request $request) {
});
Route::get('/ticket/update/{id}', [\App\Http\Controllers\Ticket\TicketUpdateController::class, 'update'], function (Request $request, int $id) {
});
Route::post('/ticket/save', [\App\Http\Controllers\Ticket\TicketSaveController::class, 'save'], function (Request $request) {
});
