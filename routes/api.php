<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\BankAccount\BankAccountCreateController;
use App\Http\Controllers\Api\BankAccount\BankAccountDeleteController;
use App\Http\Controllers\Api\BankAccount\BankAccountListController;
use App\Http\Controllers\Api\BankAccount\BankAccountReadController;
use App\Http\Controllers\Api\BankAccount\BankAccountUpdateController;
use App\Http\Controllers\Api\BankCard\BankCardCreateController;
use App\Http\Controllers\Api\BankCard\BankCardDeleteController;
use App\Http\Controllers\Api\BankCard\BankCardListController;
use App\Http\Controllers\Api\BankCard\BankCardReadController;
use App\Http\Controllers\Api\BankCard\BankCardUpdateController;
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
use App\Http\Controllers\Api\Email\EmailCreateController;
use App\Http\Controllers\Api\Email\EmailDeleteController;
use App\Http\Controllers\Api\Email\EmailListController;
use App\Http\Controllers\Api\Email\EmailReadController;
use App\Http\Controllers\Api\Email\EmailUpdateController;
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
use App\Http\Controllers\Api\Transaction\TransactionCreateController;
use App\Http\Controllers\Api\Transaction\TransactionDeleteController;
use App\Http\Controllers\Api\Transaction\TransactionListController;
use App\Http\Controllers\Api\Transaction\TransactionReadController;
use App\Http\Controllers\Api\Transaction\TransactionUpdateController;
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
| routes are loaded from bootstrap/app.php with the "/api" prefix and
| are assigned to the "api" middleware group, which is rate limited.
|
*/

/** PROTECTED ROUTES */
Route::middleware(['api'])->group(function () {
    // Brand
    Route::get('/brand', [BrandListController::class, 'index'])->middleware(['auth:api'])->can('read brand');
    Route::post('/brand', [BrandCreateController::class, 'create'])->middleware(['auth:api'])->can('create brand');
    Route::get('/brand/{id}', [BrandReadController::class, 'read'])->middleware(['auth:api'])->can('read brand');
    Route::put('/brand/{id}', [BrandUpdateController::class, 'update'])->middleware(['auth:api'])->can('update brand');
    Route::delete('/brand/{id}', [BrandDeleteController::class, 'delete'])->middleware(['auth:api'])->can('delete brand');

    // Lead
    Route::get('lead', [LeadListController::class, 'index'])->middleware(['auth:api'])->can('read lead');
    Route::get('lead/{id}', [LeadReadController::class, 'read'])->middleware(['auth:api'])->can('read lead');
    Route::post('/lead', [LeadCreateController::class, 'create'])->middleware(['auth:api'])->can('create lead');
    Route::put('/lead/{id}', [LeadUpdateController::class, 'update'])->middleware(['auth:api'])->can('update lead');
    Route::delete('/lead/{id}', [LeadDeleteController::class, 'delete'])->middleware(['auth:api'])->can('delete lead');

    // Company
    Route::get('company', [CompanyListController::class, 'index'])->middleware(['auth:api']);
    Route::get('company/{id}', [CompanyReadController::class, 'read'])->middleware(['auth:api']);
    Route::post('company', [CompanyCreateController::class, 'create'])->middleware(['auth:api']);
    Route::put('company/{id}', [CompanyUpdateController::class, 'update'])->middleware(['auth:api']);
    Route::delete('/company/{id}', [CompanyDeleteController::class, 'delete'])->middleware(['auth:api']);

    // Customer
    Route::get('customer', [CustomerListController::class, 'index'])->middleware(['auth:api'])->can('read customer');
    Route::get('customer/{id}', [CustomerReadController::class, 'read'])->middleware(['auth:api'])->can('read customer');
    Route::post('customer', [CustomerCreateController::class, 'create'])->middleware(['auth:api'])->can('create customer');
    Route::put('/customer/{id}', [CustomerUpdateController::class, 'update'])->middleware(['auth:api'])->can('update customer');
    Route::delete('/customer/{id}', [CustomerDeleteController::class, 'delete'])->middleware(['auth:api'])->can('delete customer');

    // Product
    Route::get('/product', [ProductListController::class, 'index'])->middleware(['auth:api'])->can('read product');
    Route::get('/product/{id}', [ProductReadController::class, 'read'])->middleware(['auth:api'])->can('read product');
    Route::post('product', [ProductCreateController::class, 'create'])->middleware(['auth:api'])->can('create product');
    Route::put('/product/{id}', [ProductUpdateController::class, 'update'])->middleware(['auth:api'])->can('update product');
    Route::delete('product/{id}', [ProductDeleteController::class, 'delete'])->middleware(['auth:api'])->can('delete product');

    // Contact
    Route::get('/contact', [ContactListController::class, 'index'])->middleware(['auth:api'])->can('read contact');
    Route::post('/contact', [ContactCreateController::class, 'create'])->middleware(['auth:api'])->can('create contact');
    Route::put('/contact/{id}', [ContactUpdateController::class, 'update'])->middleware(['auth:api'])->can('update contact');
    Route::delete('/contact/{id}', [ContactDeleteController::class, 'delete'])->middleware(['auth:api'])->can('delete contact');

    // User
    Route::get('/user', [UserListController::class, 'index'])->middleware(['auth:api'])->can('read user');
    Route::post('/user', [UserCreateController::class, 'create'])->middleware(['auth:api'])->can('create user');
    Route::get('/user/{id}', [UserReadController::class, 'read'])->middleware(['auth:api'])->can('read user');
    Route::put('/user/{id}', [UserUpdateController::class, 'update'])->middleware(['auth:api'])->can('update user');
    Route::delete('/user/{id}', [UserDeleteController::class, 'delete'])->middleware(['auth:api'])->can('delete user');

    // Supplier
    Route::get('/supplier', [SupplierListController::class, 'index'])->middleware(['auth:api'])->can('read supplier');
    Route::get('/supplier/{id}', [SupplierReadController::class, 'read'])->middleware(['auth:api'])->can('read supplier');
    Route::post('supplier', [SupplierCreateController::class, 'create'])->middleware(['auth:api'])->can('create supplier');
    Route::put('/supplier/{id}', [SupplierUpdateController::class, 'update'])->middleware(['auth:api'])->can('update supplier');
    Route::delete('/supplier/{id}', [SupplierDeleteController::class, 'delete'])->middleware(['auth:api'])->can('delete supplier');

    // Order
    Route::get('/order', [OrderListController::class, 'index'])->middleware(['auth:api'])->can('read order');
    Route::post('/order', [OrderCreateController::class, 'create'])->middleware(['auth:api'])->can('create order');
    Route::get('/order/{id}', [OrderReadController::class, 'read'])->middleware(['auth:api'])->can('read order');
    Route::put('/order/{id}', [OrderUpdateController::class, 'update'])->middleware(['auth:api'])->can('update order');
    Route::delete('/order/{id}', [OrderDeleteController::class, 'delete'])->middleware(['auth:api'])->can('delete order');

    // OrderItem
    Route::post('/order-item', [OrderItemCreateController::class, 'create'])->middleware(['auth:api'])->can('create order');
    Route::get('/order-item/{id}', [OrderItemReadController::class, 'read'])->middleware(['auth:api'])->can('read order');
    Route::put('/order-item/{id}', [OrderItemUpdateController::class, 'update'])->middleware(['auth:api'])->can('update order');
    Route::delete('/order-item/{id}', [OrderItemDeleteController::class, 'delete'])->middleware(['auth:api'])->can('delete order');

    // Ticket
    Route::get('/ticket', [TicketListController::class, 'index'])->middleware(['auth:api'])->can('read ticket');
    Route::post('/ticket', [TicketCreateController::class, 'create'])->middleware(['auth:api'])->can('create ticket');
    Route::get('/ticket/{id}', [TicketReadController::class, 'read'])->middleware(['auth:api'])->can('read ticket');
    Route::delete('/ticket/{id}', [TicketDeleteController::class, 'delete'])->middleware(['auth:api'])->can('delete ticket');

    // Transaction (Accounting)
    Route::get('/transaction', [TransactionListController::class, 'index'])->middleware(['auth:api'])->can('read accounting');
    Route::post('/transaction', [TransactionCreateController::class, 'create'])->middleware(['auth:api'])->can('create accounting');
    Route::get('/transaction/{id}', [TransactionReadController::class, 'read'])->middleware(['auth:api'])->can('read accounting');
    Route::put('/transaction/{id}', [TransactionUpdateController::class, 'update'])->middleware(['auth:api'])->can('update accounting');
    Route::delete('/transaction/{id}', [TransactionDeleteController::class, 'delete'])->middleware(['auth:api'])->can('delete accounting');

    // BankAccount
    Route::get('/bank-account', [BankAccountListController::class, 'index'])->middleware(['auth:api'])->can('read accounting');
    Route::get('/bank-account/{id}', [BankAccountReadController::class, 'read'])->middleware(['auth:api'])->can('read accounting');
    Route::post('/bank-account', [BankAccountCreateController::class, 'create'])->middleware(['auth:api'])->can('create accounting');
    Route::put('/bank-account/{id}', [BankAccountUpdateController::class, 'update'])->middleware(['auth:api'])->can('update accounting');
    Route::delete('/bank-account/{id}', [BankAccountDeleteController::class, 'delete'])->middleware(['auth:api'])->can('delete accounting');

    // BankCard
    Route::get('/bank-card', [BankCardListController::class, 'index'])->middleware(['auth:api'])->can('read accounting');
    Route::get('/bank-card/{id}', [BankCardReadController::class, 'read'])->middleware(['auth:api'])->can('read accounting');
    Route::post('/bank-card', [BankCardCreateController::class, 'create'])->middleware(['auth:api'])->can('create accounting');
    Route::put('/bank-card/{id}', [BankCardUpdateController::class, 'update'])->middleware(['auth:api'])->can('update accounting');
    Route::delete('/bank-card/{id}', [BankCardDeleteController::class, 'delete'])->middleware(['auth:api'])->can('delete accounting');

    // Calendar
    Route::get('/calendar', [CalendarListController::class, 'index'])->middleware(['auth:api'])->can('read calendar');
    Route::get('/calendar/{id}', [CalendarReadController::class, 'read'])->middleware(['auth:api'])->can('read calendar');
    Route::post('/calendar', [CalendarCreateController::class, 'create'])->middleware(['auth:api'])->can('create calendar');
    Route::put('/calendar/{id}', [CalendarUpdateController::class, 'update'])->middleware(['auth:api'])->can('update calendar');
    Route::delete('/calendar/{id}', [CalendarDeleteController::class, 'delete'])->middleware(['auth:api'])->can('delete calendar');

    // Email
    Route::get('/email', [EmailListController::class, 'index'])->middleware(['auth:api'])->can('read email');
    Route::get('/email/{id}', [EmailReadController::class, 'read'])->middleware(['auth:api'])->can('read email');
    Route::post('/email', [EmailCreateController::class, 'create'])->middleware(['auth:api'])->can('create email');
    Route::put('/email/{id}', [EmailUpdateController::class, 'update'])->middleware(['auth:api'])->can('update email');
    Route::delete('/email/{id}', [EmailDeleteController::class, 'delete'])->middleware(['auth:api'])->can('delete email');

    /** AUTH ROUTES */
    Route::prefix('auth')->group(function () {
        Route::post('logout', [LoginController::class, 'logout'])->middleware(['auth:api']);
        Route::post('refresh', [LoginController::class, 'refresh']);
        Route::post('me', [LoginController::class, 'me'])->middleware(['auth:api']);
    });
});

/** UNPROTECTED ROUTES */

// Login
Route::post('/auth/login', [LoginController::class, 'login'])->middleware('throttle:login');

// OpenApi Json Resource
Route::group(['middleware' => 'web'], function () {
    Route::get('/resource.json', [DocGeneratorController::class, 'render']);
});
