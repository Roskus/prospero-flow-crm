<?php

declare(strict_types=1);

use App\Http\Controllers\Rrhh\Clock\ClockInController;
use App\Http\Controllers\Rrhh\Clock\ClockOutController;
use App\Http\Controllers\Rrhh\Employee\EmployeeCreateController;
use App\Http\Controllers\Rrhh\Employee\EmployeeDeleteController;
use App\Http\Controllers\Rrhh\Employee\EmployeeIndexController;
use App\Http\Controllers\Rrhh\Employee\EmployeeSaveController;
use App\Http\Controllers\Rrhh\Employee\EmployeeShowController;
use App\Http\Controllers\Rrhh\Employee\EmployeeUpdateController;
use App\Http\Controllers\Rrhh\Holiday\HolidayDeleteController;
use App\Http\Controllers\Rrhh\Holiday\HolidayIndexController;
use App\Http\Controllers\Rrhh\Holiday\HolidaySaveController;
use App\Http\Controllers\Rrhh\Holiday\HolidayUpdateController;
use App\Http\Controllers\Rrhh\Schedule\ScheduleDeleteController;
use App\Http\Controllers\Rrhh\Schedule\ScheduleIndexController;
use App\Http\Controllers\Rrhh\Schedule\ScheduleSaveController;
use App\Http\Controllers\Rrhh\Schedule\ScheduleUpdateController;
use App\Http\Controllers\Rrhh\TimeEntry\TimeEntryIndexController;
use App\Http\Controllers\Rrhh\TimeEntry\TimeEntrySaveController;
use App\Http\Controllers\Rrhh\TimeEntry\TimeEntryUpdateController;
use App\Http\Controllers\Rrhh\TimeOff\TimeOffCreateController;
use App\Http\Controllers\Rrhh\TimeOff\TimeOffIndexController;
use App\Http\Controllers\Rrhh\TimeOff\TimeOffSaveController;
use App\Http\Controllers\Rrhh\TimeOffApproval\TimeOffApprovalIndexController;
use App\Http\Controllers\Rrhh\TimeOffApproval\TimeOffApprovalSaveController;
use Illuminate\Support\Facades\Route;

Route::prefix('rrhh')->group(function () {
    Route::get('/', [EmployeeIndexController::class, 'index'])->name('rrhh.index')->can('read rrhh');
    Route::get('/employee/create', [EmployeeCreateController::class, 'create'])->can('create rrhh');
    Route::post('/employee/save', [EmployeeSaveController::class, 'save'])->can('create rrhh')->can('update rrhh');
    Route::get('/employee/show/{id}', [EmployeeShowController::class, 'show'])->can('read rrhh');
    Route::post('/employee/update/{id}', [EmployeeUpdateController::class, 'update'])->can('update rrhh');
    Route::delete('/employee/delete/{id}', [EmployeeDeleteController::class, 'delete'])->can('delete rrhh');

    Route::get('/schedule', [ScheduleIndexController::class, 'index'])->can('read rrhh');
    Route::post('/schedule/save', [ScheduleSaveController::class, 'save'])->can('create rrhh')->can('update rrhh');
    Route::post('/schedule/update/{id}', [ScheduleUpdateController::class, 'update'])->can('update rrhh');
    Route::get('/schedule/delete/{id}', [ScheduleDeleteController::class, 'delete'])->can('delete rrhh');

    Route::post('/clock/in', [ClockInController::class, 'in'])->can('create rrhh');
    Route::post('/clock/out', [ClockOutController::class, 'out'])->can('create rrhh');
    Route::get('/time-entries', [TimeEntryIndexController::class, 'index'])->can('read rrhh');
    Route::post('/time-entries/save', [TimeEntrySaveController::class, 'save'])->can('create rrhh');
    Route::post('/time-entries/update/{id}', [TimeEntryUpdateController::class, 'update'])->can('update rrhh');

    Route::get('/time-off', [TimeOffIndexController::class, 'index'])->can('read rrhh');
    Route::get('/time-off/create', [TimeOffCreateController::class, 'create'])->can('create rrhh');
    Route::post('/time-off/save', [TimeOffSaveController::class, 'save'])->can('create rrhh')->can('update rrhh');

    Route::get('/approvals', [TimeOffApprovalIndexController::class, 'index'])->can('read rrhh');
    Route::post('/approvals/{id}/approve', [TimeOffApprovalSaveController::class, 'approve'])->can('update rrhh');
    Route::post('/approvals/{id}/reject', [TimeOffApprovalSaveController::class, 'reject'])->can('update rrhh');

    Route::get('/holidays', [HolidayIndexController::class, 'index'])->can('read rrhh');
    Route::post('/holidays/save', [HolidaySaveController::class, 'save'])->can('create rrhh')->can('update rrhh');
    Route::post('/holidays/update/{id}', [HolidayUpdateController::class, 'update'])->can('update rrhh');
    Route::delete('/holidays/delete/{id}', [HolidayDeleteController::class, 'delete'])->can('delete rrhh');
});
