<?php

declare(strict_types=1);

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

Route::get('/', [\App\Http\Controllers\MainController::class, 'index']);
// PWA - Progressive Web App
Route::get('/manifest', [\App\Http\Controllers\ManifestController::class, 'renderWebManifest'])->name('manifest');

Auth::routes(['register' => env('APP_ENV') != 'production']);

//Order
Route::get('/order', [\App\Http\Controllers\Order\OrderIndexController::class, 'index']);
Route::get('/order/create', [\App\Http\Controllers\Order\OrderCreateController::class, 'create']);
Route::get('/order/update/{id}', [\App\Http\Controllers\Order\OrderUpdateController::class, 'update']);
Route::get('/order/delete/{id}', [\App\Http\Controllers\Order\OrderDeleteController::class, 'delete']);
Route::post('/order/save', [\App\Http\Controllers\Order\OrderSaveController::class, 'save']);

//Product
Route::match(['get', 'post'], '/product', [\App\Http\Controllers\Product\ProductIndexController::class, 'index']);
Route::get('/product/create', [\App\Http\Controllers\Product\ProductCreateController::class, 'create']);
Route::get('/product/update/{id}', [\App\Http\Controllers\Product\ProductUpdateController::class, 'update']);
Route::post('/product/save', [\App\Http\Controllers\Product\ProductSaveController::class, 'save']);
Route::get('/product/import', [\App\Http\Controllers\Product\ProductImportIndexController::class, 'index']);
Route::post('/product/import/save', [\App\Http\Controllers\Product\ProductImportSaveController::class, 'save']);

Route::get('/product/delete/{id}', [\App\Http\Controllers\Product\ProductDeleteController::class, 'delete']);

//Brand
Route::get('/brand', [\App\Http\Controllers\Brand\BrandIndexController::class, 'index']);
Route::get('/brand/create', [\App\Http\Controllers\Brand\BrandCreateController::class, 'create']);
Route::get('/brand/update/{id}', [\App\Http\Controllers\Brand\BrandUpdateController::class, 'update']);
Route::get('/brand/delete/{id}', [\App\Http\Controllers\Brand\BrandDeleteController::class, 'delete']);
Route::post('/brand/save', [\App\Http\Controllers\Brand\BrandSaveController::class, 'save']);

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
Route::get('/customer/import', [\App\Http\Controllers\Customer\CustomerImportIndexController::class, 'index']);
Route::post('/customer/import/save', [\App\Http\Controllers\Customer\CustomerImportSaveController::class, 'save']);
Route::get('/customer/delete/{id}', [\App\Http\Controllers\Customer\CustomerDeleteController::class, 'delete']);
Route::get('/customer/export', [\App\Http\Controllers\Customer\CustomerExportController::class, 'export']);
Route::get('/customer/show/{customer}', [\App\Http\Controllers\Customer\CustomerShowController::class, 'show']);

//Category
Route::get('/category', [\App\Http\Controllers\Category\CategoryIndexController::class, 'index']);
Route::get('/category/create', [\App\Http\Controllers\Category\CategoryCreateController::class, 'create']);
Route::get('/category/update/{id}', [\App\Http\Controllers\Category\CategoryUpdateController::class, 'update']);
Route::get('/category/delete/{id}', [\App\Http\Controllers\Category\CategoryDeleteController::class, 'delete']);
Route::post('/category/save', [\App\Http\Controllers\Category\CategorySaveController::class, 'save']);

// Company
Route::get('/company', [\App\Http\Controllers\Company\CompanyIndexController::class, 'index']);
Route::get('/company/create', [\App\Http\Controllers\Company\CompanyCreateController::class, 'create']);
Route::get('/company/update/{id}', [\App\Http\Controllers\Company\CompanyUpdateController::class, 'update']);
Route::post('/company/save', [\App\Http\Controllers\Company\CompanySaveController::class, 'save']);
Route::get('/company/delete/{id}', [\App\Http\Controllers\Company\CompanyDeleteController::class, 'delete']);

// Contact
Route::post('/contact/save', [\App\Http\Controllers\Contact\ContactSaveController::class, 'save']);
Route::get('/contact/export-vcard/{id}', [\App\Http\Controllers\Contact\ContactExportVCard::class, 'export']);
Route::get('/contact/delete/{id}', [\App\Http\Controllers\Contact\ContactDeleteController::class, 'delete']);

// Account
Route::get('/accounting', [\App\Http\Controllers\Account\AccountIndexController::class, 'index']);
Route::post('/account/save', [\App\Http\Controllers\Account\AccountSaveController::class, 'save']);

// User
Route::get('/user', [\App\Http\Controllers\User\UserListController::class, 'index']);
Route::get('/user/create', [\App\Http\Controllers\User\UserCreateController::class, 'create']);
Route::get('/user/update/{id}', [\App\Http\Controllers\User\UserUpdateController::class, 'update']);
Route::post('/user/save', [\App\Http\Controllers\User\UserSaveController::class, 'save']);

// Profile
Route::get('/profile', [\App\Http\Controllers\Profile\ProfileUpdateController::class, 'update']);
Route::post('/profile/save', [\App\Http\Controllers\Profile\ProfileSaveController::class, 'save']);

Route::get('/setting', [\App\Http\Controllers\SettingController::class, 'index']);
Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index']);

// Calendar
Route::match(['get', 'post'], '/calendar/{date?}', [\App\Http\Controllers\Calendar\CalendarController::class, 'index'])->name('calendar.index');
Route::post('/calendar/event/save', [\App\Http\Controllers\Calendar\SaveCalendarEventController::class, 'save'])->name('calendar.save');
Route::get('/calendar/event/update/{id}', [\App\Http\Controllers\Calendar\UpdateCalendarEventController::class, 'update'])->name('calendar.update');
Route::delete('/calendar/event/delete/{id}', [\App\Http\Controllers\Calendar\DeleteCalendarEventController::class, 'delete'])->name('calendar.delete');

// Email
Route::match(['get', 'post'], '/email', [\App\Http\Controllers\Email\EmailIndexController::class, 'index']);
Route::get('/email/create', [\App\Http\Controllers\Email\EmailCreateController::class, 'create']);
Route::get('/email/view/{id}', [\App\Http\Controllers\Email\EmailViewController::class, 'view']);
Route::get('/email/update/{id}', [\App\Http\Controllers\Email\EmailUpdateController::class, 'update']);
Route::post('/email/save', [\App\Http\Controllers\Email\EmailSaveController::class, 'save']);
Route::get('/email/send/{id}', [\App\Http\Controllers\Email\EmailSendController::class, 'send']);
Route::get('/email/delete/{id}', [\App\Http\Controllers\Email\EmailDeleteController::class, 'delete']);
Route::post('/email/duplicate', [\App\Http\Controllers\Email\EmailDuplicateController::class, 'duplicate'])->name('email.duplicate');

// Email template
Route::match(['get', 'post'], '/email-template', [\App\Http\Controllers\EmailTemplate\EmailTemplateIndexController::class, 'index']);

// Campaign
Route::match(['get', 'post'], '/campaign', [\App\Http\Controllers\Campaign\CampaignIndexController::class, 'index']);
Route::get('/campaign/create', [\App\Http\Controllers\Campaign\CampaignCreateController::class, 'create']);
Route::get('/campaign/update/{id}', [\App\Http\Controllers\Campaign\CampaignUpdateController::class, 'update']);
Route::post('/campaign/save', [\App\Http\Controllers\Campaign\CampaignSaveController::class, 'save']);

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
Route::post('/ticket/message/save', [\App\Http\Controllers\Ticket\TicketCreateMessageController::class, 'save']);

//Report
Route::get('/report', [\App\Http\Controllers\Report\ReportIndexController::class, 'index']);

// Web-form
Route::get('/web-form', [\App\Http\Controllers\WebForm\WebFormIndexController::class, 'index']);

// Notifications
Route::get('/notification', [\App\Http\Controllers\Notification\GetLatestAjaxController::class, 'getLatest'])->middleware('auth');
Route::get('/notification/read/{id}', [\App\Http\Controllers\Notification\SetNotificationReadAjaxController::class, 'setRead'])->middleware('auth');

// Unsubscribe
Route::get('/unsubscribe', [\App\Http\Controllers\Unsubscribe\UnsubscribeUpdateController::class, 'update']);
Route::post('/unsubscribe/save', [\App\Http\Controllers\Unsubscribe\UnsubscribeSaveController::class, 'save']);
