## Context

The CRM already has User management with `manager_id` for org hierarchy, a basic Payroll CRUD (`app/Http/Controllers/Payroll/`), and a `work_hour` migration that was never implemented (no model, controllers, or views). This design builds on those foundations to create a complete RRHH module.

All controllers follow the existing single-responsibility pattern (one controller per action), routing in `routes/module/`, and Blade views in `resources/views/`.

## Goals / Non-Goals

**Goals:**
- Extend User with employee-specific fields (`employee_number`, `is_employee`)
- Weekly work schedule per employee with split shift support
- Clock in/out and manual time entries
- Time off requests with dual approval (manager + RRHH role)
- Company holiday calendar
- Company-level HR settings (vacation days, weekly hours)
- Improve Payroll UI
- New sidebar dropdown for RRHH module
- Granular permissions for each sub-module
- Dynamic column mapping for CustomerImport (same as LeadImport already has)

**Non-Goals:**
- Advanced payroll calculation (taxes, social security, payslip generation) — payroll is just a record of payments
- Shift swapping or shift bidding
- Advanced scheduling optimization
- Biometric/API-based clock integration — initial version is manual web-based clock
- Historical data migration from external HR systems

## Decisions

### Decision 1: Single-responsibility controllers (existing pattern)
Each action gets its own controller class (Index, Create, Save, Show, Update, Delete) under `Rrhh/{Submodule}/`. This matches the existing app convention (Lead, Customer, Payroll, User, etc.).

### Decision 2: WorkSchedule as separate table vs JSON column on User
A separate `work_schedule` table with one row per day-of-week per employee allows multiple shifts per day (split shift: 9-13 + 14-17:30) and is queryable. A JSON column would be less flexible for validation and querying.

### Decision 3: Time off uses dedicated table vs overloading work_hour
Separate `time_off` table with status workflow (pending → approved/rejected) and approval tracking. Mixing absences with clock entries would complicate the work_hour table's purpose.

### Decision 4: Vacation days as company-level config with employee override
Company table gets `vacation_days_per_year` and `weekly_hours_full_time` defaults. User table has nullable override columns. App logic reads employee override first, falls back to company default.

### Decision 5: Approval workflow is sequential (manager → RRHH)
When an employee requests time off, notification goes to their manager (from `manager_id`). After manager approves, it goes to any user with `approve timeoff` permission. Both must approve for the request to be final.

### Decision 6: Calendar uses database table vs package
Simple `company_holiday` table with date + name. No need for a full calendar library. Company holidays are loaded and displayed in a calendar view.

## Data Model

```
company
├── vacation_days_per_year (int, default 22)
└── weekly_hours_full_time (decimal, default 37.00)

user
├── employee_number (string, nullable)
├── is_employee (bool, default true)
├── vacation_days_override (int, nullable)
└── weekly_hours_override (decimal, nullable)

work_schedule
├── user_id (FK)
├── day_of_week (tinyint, 1=Monday..7=Sunday)
├── start_time (time)
├── end_time (time)
└── type (enum: 'work' | 'break')

work_hour (extend existing)
├── user_id (FK)
├── start_time (datetime)
├── end_time (datetime, nullable — for clock in progress)
├── type (enum: 'work' | 'break' | 'other', default 'work')
├── is_manual (bool, default false)
└── notes (text, nullable)

time_off
├── user_id (FK)
├── type (enum: 'vacation' | 'sick' | 'personal')
├── start_date (date)
├── end_date (date)
├── days_used (decimal)
├── reason (text, nullable)
├── status (enum: 'pending' | 'approved' | 'rejected')
├── manager_approved_by (FK user, nullable)
├── manager_approved_at (datetime, nullable)
├── rrhh_approved_by (FK user, nullable)
├── rrhh_approved_at (datetime, nullable)
└── rejected_reason (text, nullable)

company_holiday
├── company_id (FK)
├── date (date)
└── name (string)
```

## Routes

```php
// routes/module/rrhh.php

Route::prefix('rrhh')->group(function () {
    // Employees
    Route::get('/', [EmployeeIndexController::class, 'index'])->name('rrhh.index');
    Route::get('/employee/create', [EmployeeCreateController::class, 'create']);
    Route::post('/employee/save', [EmployeeSaveController::class, 'save']);
    Route::get('/employee/show/{id}', [EmployeeShowController::class, 'show']);
    Route::post('/employee/update/{id}', [EmployeeUpdateController::class, 'update']);
    Route::delete('/employee/delete/{id}', [EmployeeDeleteController::class, 'delete']);

    // Schedule
    Route::get('/schedule', [ScheduleIndexController::class, 'index']);
    Route::post('/schedule/save', [ScheduleSaveController::class, 'save']);

    // Time tracking
    Route::post('/clock/in', [ClockInController::class, 'in']);
    Route::post('/clock/out', [ClockOutController::class, 'out']);
    Route::get('/time-entries', [TimeEntryIndexController::class, 'index']);
    Route::post('/time-entries/save', [TimeEntrySaveController::class, 'save']);

    // Time off
    Route::get('/time-off', [TimeOffIndexController::class, 'index']);
    Route::get('/time-off/create', [TimeOffCreateController::class, 'create']);
    Route::post('/time-off/save', [TimeOffSaveController::class, 'save']);

    // Approval
    Route::get('/approvals', [TimeOffApprovalIndexController::class, 'index']);
    Route::post('/approvals/{id}/approve', [TimeOffApprovalSaveController::class, 'approve']);
    Route::post('/approvals/{id}/reject', [TimeOffApprovalSaveController::class, 'reject']);

    // Company holidays
    Route::get('/holidays', [HolidayIndexController::class, 'index']);
    Route::post('/holidays/save', [HolidaySaveController::class, 'save']);
    Route::delete('/holidays/delete/{id}', [HolidayDeleteController::class, 'delete']);
});
```

## Risks / Trade-offs

| Risk | Mitigation |
|---|---|
| Time off approval workflow may be too rigid if companies have different org structures | Make manager approval optional via a `requires_manager_approval` company setting |
| Work schedule with split shifts adds UI complexity | Use simple JS to add/remove time blocks per day; reuse existing patterns |
| Clock in/out without geolocation could be abused (employees clocking from home) | Log IP address on clock events for audit trail; add notes field for location |
| Vacation day balance calculation must handle partial days | Store `days_used` as decimal to allow half-day requests |
