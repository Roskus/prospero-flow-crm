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
Route::get('/manifest', [\App\Http\Controllers\ManifestController::class, 'renderWebManifest'])
    ->name('manifest');

Auth::routes(['register' => env('APP_ENV') != 'production']);

//Order
require __DIR__.'/partials/order.php';

//Product
require __DIR__.'/partials/product.php';

//Brand
require __DIR__.'/partials/brand.php';

// Lead
require __DIR__.'/partials/lead.php';

//Customer
require __DIR__.'/partials/customer.php';

//Category
require __DIR__.'/partials/category.php';

// Company
require __DIR__.'/partials/company.php';

// Contact
require __DIR__.'/partials/contact.php';

// Account
Route::get('/accounting', [\App\Http\Controllers\Account\AccountIndexController::class, 'index']);
Route::post('/account/save', [\App\Http\Controllers\Account\AccountSaveController::class, 'save']);

// User
Route::get('/user', [\App\Http\Controllers\User\UserListController::class, 'index']);
Route::get('/user/create', [\App\Http\Controllers\User\UserCreateController::class, 'create']);
Route::get('/user/update/{id}', [\App\Http\Controllers\User\UserUpdateController::class, 'update']);
Route::post('/user/save', [\App\Http\Controllers\User\UserSaveController::class, 'save']);
Route::get('/user/delete/{id}', [\App\Http\Controllers\User\UserDeleteController::class, 'delete']);

// Profile
Route::get('/profile', [\App\Http\Controllers\Profile\ProfileUpdateController::class, 'update']);
Route::post('/profile/save', [\App\Http\Controllers\Profile\ProfileSaveController::class, 'save']);

Route::get('/setting', [\App\Http\Controllers\SettingController::class, 'index']);
Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index']);

// Calendar
Route::match(['get', 'post'], '/calendar/{date?}',
    [\App\Http\Controllers\Calendar\CalendarController::class, 'index'])->name('calendar.index');
Route::post('/calendar/event/save', [\App\Http\Controllers\Calendar\SaveCalendarEventController::class, 'save'])
    ->name('calendar.save');
Route::get('/calendar/event/update/{id}',
    [\App\Http\Controllers\Calendar\UpdateCalendarEventController::class, 'update'])->name('calendar.update');
Route::delete('/calendar/event/delete/{id}',
    [\App\Http\Controllers\Calendar\DeleteCalendarEventController::class, 'delete'])->name('calendar.delete');

// Email
Route::match(['get', 'post'], '/email', [\App\Http\Controllers\Email\EmailIndexController::class, 'index']);
Route::get('/email/create', [\App\Http\Controllers\Email\EmailCreateController::class, 'create']);
Route::get('/email/view/{id}', [\App\Http\Controllers\Email\EmailViewController::class, 'view']);
Route::get('/email/update/{id}', [\App\Http\Controllers\Email\EmailUpdateController::class, 'update']);
Route::post('/email/save', [\App\Http\Controllers\Email\EmailSaveController::class, 'save']);
Route::get('/email/send/{id}', [\App\Http\Controllers\Email\EmailSendController::class, 'send']);
Route::get('/email/delete/{id}', [\App\Http\Controllers\Email\EmailDeleteController::class, 'delete']);
Route::match(['get', 'post'], '/email/duplicate',
    [\App\Http\Controllers\Email\EmailDuplicateController::class, 'duplicate'])
    ->name('email.duplicate');
Route::get('/email/download-attachment/{attachmentId}',
    [\App\Http\Controllers\Email\EmailDownloadAttachmentController::class, 'downloadAttachment'])
    ->name('downloadAttachment');
Route::get('/email/tracking/{uuid}', [\App\Http\Controllers\Email\EmailTrackingController::class, 'track_email']);

// Email template
Route::match(['get', 'post'], '/email-template',
    [\App\Http\Controllers\EmailTemplate\EmailTemplateIndexController::class, 'index']);

// Campaign
Route::match(['get', 'post'], '/campaign', [\App\Http\Controllers\Campaign\CampaignIndexController::class, 'index']);
Route::get('/campaign/create', [\App\Http\Controllers\Campaign\CampaignCreateController::class, 'create']);
Route::get('/campaign/update/{id}', [\App\Http\Controllers\Campaign\CampaignUpdateController::class, 'update']);
Route::post('/campaign/save', [\App\Http\Controllers\Campaign\CampaignSaveController::class, 'save']);

// Supplier
require __DIR__.'/partials/supplier.php';

// Ticket
require __DIR__.'/partials/ticket.php';

// Regions
Route::get('ajax/region/{country}', [\App\Http\Controllers\Region\RegionGetAjaxController::class, 'index']);

//Report
Route::get('/report', [\App\Http\Controllers\Report\ReportIndexController::class, 'index']);
Route::get('/report/sale', [\App\Http\Controllers\Report\ReportSaleController::class, 'index']);
Route::get('/report/email', [\App\Http\Controllers\Report\ReportEmailController::class, 'index']);

// Web-form
Route::get('/web-form', [\App\Http\Controllers\WebForm\WebFormIndexController::class, 'index']);

// Notifications
Route::get('/notification', [\App\Http\Controllers\Notification\NotificationIndexController::class, 'index']);
Route::get('/ajax/notification', [\App\Http\Controllers\Notification\GetLatestAjaxController::class, 'getLatest'])
    ->middleware('auth');
Route::get('/notification/read/{id}',
    [\App\Http\Controllers\Notification\SetNotificationReadAjaxController::class, 'setRead'])
    ->middleware('auth');
Route::get('/notification/delete/{id}',
    [\App\Http\Controllers\Notification\DeleteNotificationController::class, 'delete']);

// Unsubscribe
Route::get('/unsubscribe', [\App\Http\Controllers\Unsubscribe\UnsubscribeUpdateController::class, 'update']);
Route::post('/unsubscribe/save', [\App\Http\Controllers\Unsubscribe\UnsubscribeSaveController::class, 'save']);

// Permission
Route::get('/permission', [\App\Http\Controllers\Permission\PermissionIndexController::class, 'index']);
Route::post('/permission', [\App\Http\Controllers\Permission\PermissionSaveController::class, 'save']);

//Bank
require __DIR__.'/partials/bank.php';

//Bank account
require __DIR__.'/partials/bank_account.php';

// Two Factor Authentication
require __DIR__.'/partials/two_factor_authentication.php';
