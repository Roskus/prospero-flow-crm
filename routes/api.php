<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\BankAccount\BankAccountCreateController;
use App\Http\Controllers\Api\BankAccount\BankAccountDeleteController;
use App\Http\Controllers\Api\BankAccount\BankAccountListController;
use App\Http\Controllers\Api\BankAccount\BankAccountReadController;
use App\Http\Controllers\Api\BankAccount\BankAccountUpdateController;
use App\Http\Controllers\Api\Brand\BrandCreateController;
use App\Http\Controllers\Api\Brand\BrandDeleteController;
use App\Http\Controllers\Api\Brand\BrandListController;
use App\Http\Controllers\Api\Brand\BrandReadController;
use App\Http\Controllers\Api\Brand\BrandUpdateController;
use App\Http\Controllers\Api\Calendar\CalendarCreateController;
use App\Http\Controllers\Api\Calendar\CalendarDeleteController;
use App\Http\Controllers\Api\Calendar\CalendarListController;
use App\Http\Controllers\Api\Calendar\CalendarReadController;
use App\Http\Controllers\Api\Calendar\CalendarUpdateController;
use App\Http\Controllers\Api\Company\CompanyCreateController;
use App\Http\Controllers\Api\Company\CompanyDeleteController;
use App\Http\Controllers\Api\Company\CompanyListController;
use App\Http\Controllers\Api\Company\CompanyReadController;
use App\Http\Controllers\Api\Company\CompanyUpdateController;
use App\Http\Controllers\Api\Contact\ContactCreateController;
use App\Http\Controllers\Api\Contact\ContactDeleteController;
use App\Http\Controllers\Api\Contact\ContactListController;
use App\Http\Controllers\Api\Contact\ContactUpdateController;
use App\Http\Controllers\Api\Customer\CustomerCreateController;
use App\Http\Controllers\Api\Customer\CustomerDeleteController;
use App\Http\Controllers\Api\Customer\CustomerListController;
use App\Http\Controllers\Api\Customer\CustomerReadController;
use App\Http\Controllers\Api\Customer\CustomerUpdateController;
use App\Http\Controllers\Api\Doc\DocGeneratorController;
use App\Http\Controllers\Api\Email\EmailDeleteController;
use App\Http\Controllers\Api\Lead\LeadCreateController;
use App\Http\Controllers\Api\Lead\LeadDeleteController;
use App\Http\Controllers\Api\Lead\LeadListController;
use App\Http\Controllers\Api\Lead\LeadReadController;
use App\Http\Controllers\Api\Lead\LeadUpdateController;
use App\Http\Controllers\Api\Order\OrderCreateController;
use App\Http\Controllers\Api\Order\OrderDeleteController;
use App\Http\Controllers\Api\Order\OrderItemCreateController;
use App\Http\Controllers\Api\Order\OrderItemDeleteController;
use App\Http\Controllers\Api\Order\OrderItemReadController;
use App\Http\Controllers\Api\Order\OrderItemUpdateController;
use App\Http\Controllers\Api\Order\OrderListController;
use App\Http\Controllers\Api\Order\OrderReadController;
use App\Http\Controllers\Api\Order\OrderUpdateController;
use App\Http\Controllers\Api\Product\ProductCreateController;
use App\Http\Controllers\Api\Product\ProductDeleteController;
use App\Http\Controllers\Api\Product\ProductListController;
use App\Http\Controllers\Api\Product\ProductReadController;
use App\Http\Controllers\Api\Product\ProductUpdateController;
use App\Http\Controllers\Api\Supplier\SupplierCreateController;
use App\Http\Controllers\Api\Supplier\SupplierDeleteController;
use App\Http\Controllers\Api\Supplier\SupplierListController;
use App\Http\Controllers\Api\Supplier\SupplierReadController;
use App\Http\Controllers\Api\Supplier\SupplierUpdateController;
use App\Http\Controllers\Api\Ticket\TicketCreateController;
use App\Http\Controllers\Api\Ticket\TicketDeleteController;
use App\Http\Controllers\Api\Ticket\TicketListController;
use App\Http\Controllers\Api\Ticket\TicketReadController;
use App\Http\Controllers\Api\User\UserCreateController;
use App\Http\Controllers\Api\User\UserDeleteController;
use App\Http\Controllers\Api\User\UserListController;
use App\Http\Controllers\Api\User\UserReadController;
use App\Http\Controllers\Api\User\UserUpdateController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/** PROTECTED ROUTES */
Route::middleware(['api'])->group(function () {
    // Brand
    Route::get('/brand', [BrandListController::class, 'index'])->middleware(['auth:api']);
    Route::post('/brand', [BrandCreateController::class, 'create'])->middleware(['auth:api']);
    Route::get('/brand/{id}', [BrandReadController::class, 'read'])->middleware(['auth:api']);
    Route::put('/brand/{id}', [BrandUpdateController::class, 'update'])->middleware(['auth:api']);
    Route::delete('/brand/{id}', [BrandDeleteController::class, 'delete'])->middleware(['auth:api']);

    // Lead
    Route::get('lead', [LeadListController::class, 'index'])->middleware(['auth:api']);
    Route::get('lead/{id}', [LeadReadController::class, 'read'])->middleware(['auth:api']);
    Route::post('/lead', [LeadCreateController::class, 'create'])->middleware(['auth:api']);
    Route::put('/lead/{id}', [LeadUpdateController::class, 'update'])->middleware(['auth:api']);
    Route::delete('/lead/{id}', [LeadDeleteController::class, 'delete'])->middleware(['auth:api']);

    // Company
    Route::get('company', [CompanyListController::class, 'index'])->middleware(['auth:api']);
    Route::get('company/{id}', [CompanyReadController::class, 'read'])->middleware(['auth:api']);
    Route::post('company', [CompanyCreateController::class, 'create'])->middleware(['auth:api']);
    Route::put('company/{id}', [CompanyUpdateController::class, 'update'])->middleware(['auth:api']);
    Route::delete('/company/{id}', [CompanyDeleteController::class, 'delete'])->middleware(['auth:api']);

    // Customer
    Route::get('customer', [CustomerListController::class, 'index'])->middleware(['auth:api']);
    Route::get('customer/{id}', [CustomerReadController::class, 'read'])->middleware(['auth:api']);
    Route::post('customer', [CustomerCreateController::class, 'create'])->middleware(['auth:api']);
    Route::put('/customer/{id}', [CustomerUpdateController::class, 'update'])->middleware(['auth:api']);
    Route::delete('/customer/{id}', [CustomerDeleteController::class, 'delete'])->middleware(['auth:api']);

    // Product
    Route::get('/product', [ProductListController::class, 'index'])->middleware(['auth:api']);
    Route::get('/product/{id}', [ProductReadController::class, 'read'])->middleware(['auth:api']);
    Route::post('product', [ProductCreateController::class, 'create'])->middleware(['auth:api']);
    Route::put('/product/{id}', [ProductUpdateController::class, 'update'])->middleware(['auth:api']);
    Route::delete('product/{id}', [ProductDeleteController::class, 'delete'])->middleware(['auth:api']);

    // Contact
    Route::get('/contact', [ContactListController::class, 'index'])->middleware(['auth:api']);
    Route::post('/contact', [ContactCreateController::class, 'create'])->middleware(['auth:api']);
    Route::put('/contact/{id}', [ContactUpdateController::class, 'update'])->middleware(['auth:api']);
    Route::delete('/contact/{id}', [ContactDeleteController::class, 'delete'])->middleware(['auth:api']);

    // User
    Route::get('/user', [UserListController::class, 'index'])->middleware(['auth:api']);
    Route::post('/user', [UserCreateController::class, 'create'])->middleware(['auth:api']);
    Route::get('/user/{id}', [UserReadController::class, 'read'])->middleware(['auth:api']);
    Route::put('/user/{id}', [UserUpdateController::class, 'update'])->middleware(['auth:api']);
    Route::delete('/user/{id}', [UserDeleteController::class, 'delete'])->middleware(['auth:api']);

    // Supplier
    Route::get('/supplier', [SupplierListController::class, 'index'])->middleware(['auth:api']);
    Route::get('/supplier/{id}', [SupplierReadController::class, 'read'])->middleware(['auth:api']);
    Route::post('supplier', [SupplierCreateController::class, 'create'])->middleware(['auth:api']);
    Route::put('/supplier/{id}', [SupplierUpdateController::class, 'update'])->middleware(['auth:api']);
    Route::delete('/supplier/{id}', [SupplierDeleteController::class, 'delete'])->middleware(['auth:api']);

    // Order
    Route::get('/order', [OrderListController::class, 'index'])->middleware(['auth:api']);
    Route::post('/order', [OrderCreateController::class, 'create'])->middleware(['auth:api']);
    Route::get('/order/{id}', [OrderReadController::class, 'read'])->middleware(['auth:api']);
    Route::put('/order/{id}', [OrderUpdateController::class, 'update'])->middleware(['auth:api']);
    Route::delete('/order/{id}', [OrderDeleteController::class, 'delete'])->middleware(['auth:api']);

    // OrderItem
    Route::post('/order-item', [OrderItemCreateController::class, 'create'])->middleware(['auth:api']);
    Route::get('/order-item/{id}', [OrderItemReadController::class, 'read'])->middleware(['auth:api']);
    Route::put('/order-item/{id}', [OrderItemUpdateController::class, 'update'])->middleware(['auth:api']);
    Route::delete('/order-item/{id}', [OrderItemDeleteController::class, 'delete'])->middleware(['auth:api']);

    // Ticket
    Route::get('/ticket', [TicketListController::class, 'index'])->middleware(['auth:api']);
    Route::post('/ticket', [TicketCreateController::class, 'create'])->middleware(['auth:api']);
    Route::get('/ticket/{id}', [TicketReadController::class, 'read'])->middleware(['auth:api']);
    Route::delete('/ticket/{id}', [TicketDeleteController::class, 'delete'])->middleware(['auth:api']);

    // BankAccount
    Route::get('/bank-account', [BankAccountListController::class, 'index'])->middleware(['auth:api']);
    Route::get('/bank-account/{id}', [BankAccountReadController::class, 'read'])->middleware(['auth:api']);
    Route::post('/bank-account', [BankAccountCreateController::class, 'create'])->middleware(['auth:api']);
    Route::put('/bank-account/{id}', [BankAccountUpdateController::class, 'update'])->middleware(['auth:api']);
    Route::delete('/bank-account/{id}', [BankAccountDeleteController::class, 'delete'])->middleware(['auth:api']);

    // Calendar
    Route::get('/calendar', [CalendarListController::class, 'index'])->middleware(['auth:api']);
    Route::get('/calendar/{id}', [CalendarReadController::class, 'read'])->middleware(['auth:api']);
    Route::post('/calendar', [CalendarCreateController::class, 'create'])->middleware(['auth:api']);
    Route::put('/calendar/{id}', [CalendarUpdateController::class, 'update'])->middleware(['auth:api']);
    Route::delete('/calendar/{id}', [CalendarDeleteController::class, 'delete'])->middleware(['auth:api']);

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
    Route::get('/resource.json', [DocGeneratorController::class, 'render']);
});
