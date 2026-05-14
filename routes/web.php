<?php

declare(strict_types=1);

use App\Http\Controllers\Account\AccountIndexController;
use App\Http\Controllers\Account\AccountSaveController;
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

// Order
require_once __DIR__.'/module/order.php';

// Product
require_once __DIR__.'/module/product.php';

// Brand
require_once __DIR__.'/module/brand.php';

// Lead
require_once __DIR__.'/module/lead.php';

// Customer
require_once __DIR__.'/module/customer.php';

// Category
require_once __DIR__.'/module/category.php';

// Company
require_once __DIR__.'/module/company.php';

// Contact
require_once __DIR__.'/module/contact.php';

// Account
Route::get('/accounting', [AccountIndexController::class, 'index']);
Route::post('/account/save', [AccountSaveController::class, 'save']);

// User
require_once __DIR__.'/module/user.php';

// Profile
Route::get('/profile', [ProfileUpdateController::class, 'update']);
Route::post('/profile/save', [ProfileSaveController::class, 'save']);

Route::get('/setting', [SettingController::class, 'index']);
Route::get('/home', [HomeController::class, 'index']);

// Calendar
require_once __DIR__.'/module/calendar.php';

// Email
require_once __DIR__.'/module/email.php';

// Email template
Route::match(['get', 'post'], '/email-template',
    [EmailTemplateIndexController::class, 'index']);

// Campaign
require_once __DIR__.'/module/campaign.php';

// Supplier
require_once __DIR__.'/module/supplier.php';

// Ticket
require_once __DIR__.'/module/ticket.php';

// Regions
Route::get('ajax/region/{country}', [RegionGetAjaxController::class, 'index']);

// Report
Route::get('/report', [ReportIndexController::class, 'index']);
Route::get('/report/sale', [ReportSaleController::class, 'index']);
Route::get('/report/email', [ReportEmailController::class, 'index']);

// Web-form
Route::get('/web-form', [WebFormIndexController::class, 'index']);

// Notifications
require_once __DIR__.'/module/notification.php';

// Unsubscribe
Route::get('/unsubscribe', [UnsubscribeUpdateController::class, 'update']);
Route::post('/unsubscribe/save', [UnsubscribeSaveController::class, 'save']);

// Permission
Route::get('/permission', [PermissionIndexController::class, 'index']);
Route::post('/permission', [PermissionSaveController::class, 'save']);

// Bank
require_once __DIR__.'/module/bank.php';

// Bank account
require_once __DIR__.'/module/bank_account.php';

// Payroll
require_once __DIR__.'/module/payroll.php';
