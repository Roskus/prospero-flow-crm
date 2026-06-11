<?php

declare(strict_types=1);

use App\Http\Controllers\Account\AccountCreateController;
use App\Http\Controllers\Account\AccountDeleteController;
use App\Http\Controllers\Account\AccountIndexController;
use App\Http\Controllers\Account\AccountSaveController;
use App\Http\Controllers\Account\AccountUpdateController;
use App\Http\Controllers\Auth\LockController;
use App\Http\Controllers\Auth\UnlockController;
use App\Http\Controllers\EmailTemplate\EmailTemplateIndexController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ManifestController;
use App\Http\Controllers\Permission\PermissionIndexController;
use App\Http\Controllers\Permission\PermissionSaveController;
use App\Http\Controllers\Profile\ProfileSaveController;
use App\Http\Controllers\Profile\ProfileUpdateController;
use App\Http\Controllers\Region\RegionGetAjaxController;
use App\Http\Controllers\Report\ReportEmailController;
use App\Http\Controllers\Report\ReportIndexController;
use App\Http\Controllers\Report\ReportSaleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Unsubscribe\UnsubscribeSaveController;
use App\Http\Controllers\Unsubscribe\UnsubscribeUpdateController;
use App\Http\Controllers\WebForm\WebFormIndexController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [MainController::class, 'index']);
// PWA - Progressive Web App
Route::get('/manifest', [ManifestController::class, 'renderWebManifest'])
    ->name('manifest');

Auth::routes(['register' => env('APP_ENV') != 'production']);

Route::get('/lock', [LockController::class, 'index']);
Route::post('/unlock', [UnlockController::class, 'unlock'])->name('unlock');

// Important use require insted of require_once for test loading.

// Order
require __DIR__.'/module/order.php';

// Product
require __DIR__.'/module/product.php';

// Brand
require __DIR__.'/module/brand.php';

// Lead
require __DIR__.'/module/lead.php';

// Customer
require __DIR__.'/module/customer.php';

// Category
require __DIR__.'/module/category.php';

// Company
require __DIR__.'/module/company.php';

// Contact
require __DIR__.'/module/contact.php';

// Account
require __DIR__.'/module/account_category.php';

Route::get('/accounting', [AccountIndexController::class, 'index']);
Route::get('/account/create', [AccountCreateController::class, 'create']);
Route::get('/account/edit/{id}', [AccountUpdateController::class, 'update']);
Route::post('/account/save', [AccountSaveController::class, 'save']);
Route::delete('/account/delete/{id}', [AccountDeleteController::class, 'delete']);

// User
require __DIR__.'/module/user.php';

// Profile
Route::get('/profile', [ProfileUpdateController::class, 'update']);
Route::post('/profile/save', [ProfileSaveController::class, 'save']);

Route::get('/setting', [SettingController::class, 'index']);
Route::get('/home', [HomeController::class, 'index']);

// Calendar
require __DIR__.'/module/calendar.php';

// Email
require __DIR__.'/module/email.php';

// Email template
Route::match(['get', 'post'], '/email-template',
    [EmailTemplateIndexController::class, 'index']);

// Campaign
require __DIR__.'/module/campaign.php';

// Supplier
require __DIR__.'/module/supplier.php';

// Ticket
require __DIR__.'/module/ticket.php';

// Regions
Route::get('ajax/region/{country}', [RegionGetAjaxController::class, 'index']);

// Report
Route::get('/report', [ReportIndexController::class, 'index']);
Route::get('/report/sale', [ReportSaleController::class, 'index']);
Route::get('/report/email', [ReportEmailController::class, 'index']);

// Web-form
Route::get('/web-form', [WebFormIndexController::class, 'index']);

// Notifications
require __DIR__.'/module/notification.php';

// Unsubscribe
Route::get('/unsubscribe', [UnsubscribeUpdateController::class, 'update']);
Route::post('/unsubscribe/save', [UnsubscribeSaveController::class, 'save']);

// Permission
Route::get('/permission', [PermissionIndexController::class, 'index']);
Route::post('/permission', [PermissionSaveController::class, 'save']);

// Bank
require __DIR__.'/module/bank.php';

// Bank account
require __DIR__.'/module/bank_account.php';

// Payroll
require __DIR__.'/module/payroll.php';
