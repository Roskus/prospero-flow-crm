<?php

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

Auth::routes(['register' => env('APP_ENV') != 'production']);

//Order
Route::get('/order', 'OrderController@index');
Route::get('/order/add', 'OrderController@add');
Route::get('/order/edit/{id}', 'OrderController@edit');
Route::post('/order/save', 'OrderController@save');

//Product
Route::match(['get', 'post'], '/product', 'ProductController@index');
Route::get('/product/create', [\App\Http\Controllers\Product\ProductCreateController::class, 'create']);
Route::get('/product/update/{id}', [\App\Http\Controllers\Product\ProductUpdateController::class, 'update']);
Route::post('/product/save', [\App\Http\Controllers\Product\ProductSaveController::class, 'save']);
Route::get('/product/delete/{id}', [\App\Http\Controllers\Product\ProductDeleteController::class, 'delete']);

//Brand
Route::get('/brand', 'BrandController@index');
Route::get('/brand/add', 'BrandController@add');
Route::get('/brand/edit/{id}', 'BrandController@edit');
Route::post('/brand/save', 'BrandController@save');

// Lead
Route::match(['get', 'post'], '/lead', [\App\Http\Controllers\Lead\LeadIndexController::class, 'index']);
Route::get('/lead/create', [\App\Http\Controllers\Lead\LeadCreateController::class, 'create']);
Route::get('/lead/update/{id}', [\App\Http\Controllers\Lead\LeadUpdateController::class, 'update']);
Route::get('/lead/promote/{id}', [\App\Http\Controllers\Lead\LeadPromoteCustomerController::class, 'promote']);
Route::post('/lead/save', [\App\Http\Controllers\Lead\LeadSaveController::class, 'save']);
Route::get('/lead/import', [\App\Http\Controllers\Lead\LeadImportIndexController::class, 'index']);
Route::post('/lead/import/save', [\App\Http\Controllers\Lead\LeadImportSaveController::class, 'save']);
Route::get('/lead/delete/{id}', [\App\Http\Controllers\Lead\LeadDeleteController::class, 'delete']);
Route::get('/lead/export', [\App\Http\Controllers\Lead\LeadExportController::class, 'export']);

//Customer
Route::match(['get', 'post'], '/customer', [\App\Http\Controllers\Customer\CustomerIndexController::class, 'index']);
Route::get('/customer/create', [\App\Http\Controllers\Customer\CustomerCreateController::class, 'create']);
Route::get('/customer/update/{id}', [\App\Http\Controllers\Customer\CustomerUpdateController::class, 'update']);
Route::post('/customer/save', [\App\Http\Controllers\Customer\CustomerSaveController::class, 'save']);
Route::get('/customer/delete/{id}', [\App\Http\Controllers\Customer\CustomerDeleteController::class, 'delete']);

//Category
Route::get('/category', 'CategoryController@index');
Route::get('/category/add', 'CategoryController@add');
Route::get('/category/edit/{id}', 'CategoryController@edit');
Route::post('/category/save', 'CategoryController@save');

// Company
Route::get('/company', [\App\Http\Controllers\Company\CompanyIndexController::class, 'index']);
Route::get('/company/create', [\App\Http\Controllers\Company\CompanyCreateController::class, 'create']);
Route::get('/company/update/{id}', [\App\Http\Controllers\Company\CompanyUpdateController::class, 'update']);
Route::post('/company/save', [\App\Http\Controllers\Company\CompanySaveController::class, 'save']);
Route::get('/company/delete/{id}', [\App\Http\Controllers\Company\CompanyDeleteController::class, 'delete']);

// Account
Route::get('/accounting', 'AccountController@index');
Route::post('/account/save', 'AccountController@save');

// User
Route::get('/user', [\App\Http\Controllers\User\UserListController::class, 'index']);
Route::get('/user/create', [\App\Http\Controllers\User\UserCreateController::class, 'create']);
Route::get('/user/update/{id}', [\App\Http\Controllers\User\UserUpdateController::class, 'update']);
Route::post('/user/save', [\App\Http\Controllers\User\UserSaveController::class, 'save']);

// Profile
Route::get('/profile', 'ProfileController@edit');
Route::post('/profile/save', [\App\Http\Controllers\Profile\ProfileSaveController::class, 'save']);

Route::get('/setting', 'SettingController@index');
Route::get('/home', 'HomeController@index');

// Calendar
Route::match(['get', 'post'], '/calendar', [\App\Http\Controllers\Calendar\CalendarController::class, 'index']);

// Email
Route::match(['get', 'post'], '/email', [\App\Http\Controllers\Email\EmailIndexController::class, 'index']);
Route::get('/email/create', [\App\Http\Controllers\Email\EmailCreateController::class, 'create']);
Route::get('/email/update/{id}', [\App\Http\Controllers\Email\EmailUpdateController::class, 'update']);
Route::post('/email/save', [\App\Http\Controllers\Email\EmailSaveController::class, 'save']);
Route::get('/email/send/{id}', [\App\Http\Controllers\Email\EmailSendController::class, 'send']);
Route::get('/email/delete/{id}', [\App\Http\Controllers\Email\EmailDeleteController::class, 'delete']);

// Email template
Route::match(['get', 'post'], '/email-template', [\App\Http\Controllers\EmailTemplate\EmailTemplateIndexController::class, 'index']);

// Supplier
Route::match(['get', 'post'], '/supplier', [\App\Http\Controllers\Supplier\SupplierIndexController::class, 'index']);
Route::get('/supplier/create', [\App\Http\Controllers\Supplier\SupplierCreateController::class, 'create']);
Route::get('/supplier/update/{id}', [\App\Http\Controllers\Supplier\SupplierUpdateController::class, 'update']);
Route::post('/supplier/save', [\App\Http\Controllers\Supplier\SupplierSaveController::class, 'save']);

// Ticket
Route::match(['get', 'post'], '/ticket', [\App\Http\Controllers\Ticket\TicketIndexController::class, 'index']);
Route::get('/ticket/create', [\App\Http\Controllers\Ticket\TicketCreateController::class, 'create']);
Route::get('/ticket/update/{id}', [\App\Http\Controllers\Ticket\TicketUpdateController::class, 'update']);
Route::post('/ticket/save', [\App\Http\Controllers\Ticket\TicketSaveController::class, 'save']);
Route::get('/ticket/delete/{id}', [\App\Http\Controllers\Ticket\TicketDeleteController::class, 'delete']);

//Report
Route::get('/report', [\App\Http\Controllers\Report\ReportIndexController::class, 'index']);

// Web-form
Route::get('/web-form', [\App\Http\Controllers\WebForm\WebFormIndexController::class, 'index']);

// Unsubscribe
Route::get('/unsubscribe', [\App\Http\Controllers\Unsubscribe\UnsubscribeUpdateController::class, 'update']);
Route::post('/unsubscribe/save', [\App\Http\Controllers\Unsubscribe\UnsubscribeSaveController::class, 'save']);
