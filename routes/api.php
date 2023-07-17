<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Brand\BrandDeleteController;
use App\Http\Controllers\Api\Brand\BrandListController;
use App\Http\Controllers\Api\Brand\BrandReadController;
use App\Http\Controllers\Api\Company\CompanyDeleteController;
use App\Http\Controllers\Api\Contact\ContactDeleteController;
use App\Http\Controllers\Api\Customer\CustomerDeleteController;
use App\Http\Controllers\Api\Email\EmailDeleteController;
use App\Http\Controllers\Api\Lead\LeadDeleteController;
use App\Http\Controllers\Api\Order\OrderDeleteController;
use App\Http\Controllers\Api\Supplier\SupplierDeleteController;
use App\Http\Controllers\Api\User\UserDeleteController;
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
    // Brand
    Route::get('/brand', [BrandListController::class, 'index'])->middleware(['auth:api']);
    Route::get('/brand/{id}', [BrandReadController::class, 'read'])->middleware(['auth:api']);
    Route::delete('/brand/{id}', [BrandDeleteController::class, 'delete'])->middleware(['auth:api']);

    // Lead
    Route::get('lead', [\App\Http\Controllers\Api\Lead\LeadListController::class, 'index'])->middleware(['auth:api']);
    Route::get('lead/{id}', [\App\Http\Controllers\Api\Lead\LeadReadController::class, 'read'])->middleware(['auth:api']);
    Route::post('lead', [\App\Http\Controllers\Api\Lead\LeadCreateController::class, 'create'])->middleware(['auth:api']);
    Route::delete('/lead/{id}', [LeadDeleteController::class, 'delete'])->middleware(['auth:api']);

    // Company
    Route::get('company', [\App\Http\Controllers\Api\Company\CompanyListController::class, 'index'])->middleware(['auth:api']);
    Route::get('company/{id}', [\App\Http\Controllers\Api\Company\CompanyReadController::class, 'read'])->middleware(['auth:api']);
    Route::post('company', [\App\Http\Controllers\Api\Company\CompanyCreateController::class, 'create'])->middleware(['auth:api']);
    Route::put('company/{id}', [\App\Http\Controllers\Api\Company\CompanyUpdateController::class, 'update'])->middleware(['auth:api']);
    Route::delete('/company/{id}', [CompanyDeleteController::class, 'delete'])->middleware(['auth:api']);

    // Customer
    Route::get('customer', [\App\Http\Controllers\Api\Customer\CustomerListController::class, 'index'])->middleware(['auth:api']);
    Route::get('customer/{id}', [\App\Http\Controllers\Api\Customer\CustomerReadController::class, 'read'])->middleware(['auth:api']);
    Route::post('customer', [\App\Http\Controllers\Api\Customer\CustomerCreateController::class, 'create'])->middleware(['auth:api']);
    Route::delete('/customer/{id}', [CustomerDeleteController::class, 'delete'])->middleware(['auth:api']);

    // Product
    Route::get('/product', [App\Http\Controllers\Api\Product\ProductListController::class, 'index'])->middleware(['auth:api']);
    Route::get('/product/{id}', [App\Http\Controllers\Api\Product\ProductReadController::class, 'read'])->middleware(['auth:api']);
    Route::post('product', [\App\Http\Controllers\Api\Product\ProductCreateController::class, 'create'])->middleware(['auth:api']);
    Route::put('product', [\App\Http\Controllers\Api\Product\ProductUpdateController::class, 'update'])->middleware(['auth:api']);
    Route::delete('product', [\App\Http\Controllers\Api\Product\ProductDeleteController::class, 'delete'])->middleware(['auth:api']);

    // Contact
    Route::get('/contact', [\App\Http\Controllers\Api\Contact\ContactListController::class, 'index'])->middleware(['auth:api']);
    Route::post('/contact', [\App\Http\Controllers\Api\Contact\ContactCreateController::class, 'create'])->middleware(['auth:api']);
    //Route::patch('/contact/{id}', 'Api\Contact\ContactUpdateController@update');
    Route::delete('/contact/{id}', [ContactDeleteController::class, 'delete'])->middleware(['auth:api']);

    // User
    Route::get('/user', [\App\Http\Controllers\Api\User\UserListController::class, 'index'])->middleware(['auth:api']);
    Route::get('/user/{id}', [\App\Http\Controllers\Api\User\UserReadController::class, 'read'])->middleware(['auth:api']);
    Route::delete('/user/{id}', [UserDeleteController::class, 'delete'])->middleware(['auth:api']);

    // Supplier
    Route::get('/supplier', [App\Http\Controllers\Api\Supplier\SupplierListController::class, 'index'])->middleware(['auth:api']);
    Route::get('/supplier/{id}', [App\Http\Controllers\Api\Supplier\SupplierReadController::class, 'read'])->middleware(['auth:api']);
    Route::post('supplier', [\App\Http\Controllers\Api\Supplier\SupplierCreateController::class, 'create'])->middleware(['auth:api']);
    Route::delete('/supplier/{id}', [SupplierDeleteController::class, 'delete'])->middleware(['auth:api']);

    // Order
    Route::get('/order', [App\Http\Controllers\Api\Order\OrderListController::class, 'index'])->middleware(['auth:api']);
    Route::delete('/order/{id}', [OrderDeleteController::class, 'delete'])->middleware(['auth:api']);

    // Email
    Route::delete('/email/{id}', [EmailDeleteController::class, 'delete'])->middleware(['auth:api']);

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
Route::group(['middleware' => 'web'], function () {
    Route::get('/resource.json', [\App\Http\Controllers\Api\Doc\DocGeneratorController::class, 'render']);
});
