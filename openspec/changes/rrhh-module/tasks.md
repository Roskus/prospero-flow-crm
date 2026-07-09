## 1. Database Migrations

- [x] 1.1 Create migration to add `employee_number`, `is_employee`, `vacation_days_override`, `weekly_hours_override` to user table
- [x] 1.2 Create migration to add `vacation_days_per_year`, `weekly_hours_full_time` to company table
- [x] 1.3 Create `work_schedule` table migration (user_id, day_of_week, start_time, end_time, type)
- [x] 1.4 Create migration to add `type`, `is_manual`, `notes` columns to existing `work_hour` table
- [x] 1.5 Create `time_off` table migration (user_id, type, start_date, end_date, days_used, reason, status, approval fields)
- [x] 1.6 Create `company_holiday` table migration (company_id, date, name)

## 2. Models

- [x] 2.1 Add employee fields to User model (`employee_number`, `is_employee`, `vacation_days_override`, `weekly_hours_override`) with casts and validation
- [x] 2.2 Add HR config fields to Company model (`vacation_days_per_year`, `weekly_hours_full_time`)
- [x] 2.3 Create WorkSchedule model with relationships and fillable attributes
- [x] 2.4 Create TimeOff model with status workflow methods
- [x] 2.5 Create CompanyHoliday model
- [x] 2.6 Extend existing Payroll model with employee relationship if missing

## 3. Employee Management

- [x] 3.1 Create `EmployeeIndexController` with search, pagination, and proper permission check
- [x] 3.2 Create `EmployeeCreateController` with form view
- [x] 3.3 Create `EmployeeSaveController` with validation for `employee_number` uniqueness
- [x] 3.4 Create `EmployeeShowController` with aggregated profile (hours, time off, payroll)
- [x] 3.5 Create `EmployeeUpdateController` with form view
- [x] 3.6 Create `EmployeeDeleteController` with soft delete
- [x] 3.7 Create employee index view with table and search
- [x] 3.8 Create employee form view (create/update)
- [x] 3.9 Create employee show view with profile layout

## 4. Work Schedule

- [x] 4.1 Create `ScheduleIndexController` to display employee's schedule
- [x] 4.2 Create `ScheduleSaveController` to save weekly schedule with split shift support
- [x] 4.3 Create schedule view with day-by-day time block editor

## 5. Time Tracking

- [x] 5.1 Create `ClockInController` with open-entry check
- [x] 5.2 Create `ClockOutController` with open-entry validation
- [x] 5.3 Create `TimeEntryIndexController` with filters and totals
- [x] 5.4 Create `TimeEntrySaveController` for manual entries
- [x] 5.5 Create clock in/out button view (simple widget)
- [x] 5.6 Create time entry list view with filters and totals

## 6. Time Off

- [x] 6.1 Create `TimeOffIndexController` showing employee's requests and remaining balance
- [x] 6.2 Create `TimeOffCreateController` with form
- [x] 6.3 Create `TimeOffSaveController` with balance validation
- [x] 6.4 Create `TimeOffApprovalIndexController` showing pending requests
- [x] 6.5 Create `TimeOffApprovalSaveController` with approve/reject actions
- [x] 6.6 Create time off list view
- [x] 6.7 Create time off form view
- [x] 6.8 Create approval queue view

## 7. Company Holidays

- [x] 7.1 Create `HolidayIndexController` with calendar display
- [x] 7.2 Create `HolidaySaveController` with duplicate date validation
- [x] 7.3 Create `HolidayDeleteController`
- [x] 7.4 Create holiday calendar view

## 8. Payroll Improvements

- [ ] 8.1 Update PayrollIndexController to filter by employee and year, improve view
- [ ] 8.2 Update payroll index view with employee column and better layout
- [ ] 8.3 Update PayrollCreateController and save controller if needed

## 9. Permissions, Menu & Routing

- [x] 9.1 Create `routes/module/rrhh.php` with all routes
- [x] 9.2 Add new permissions to PermissionSeeder (`read rrhh`, `create rrhh`, `update rrhh`, `delete rrhh`, `approve timeoff`, `manage schedule`, `manage holidays`)
- [x] 9.3 Add RRHH dropdown to `menu.php` with all sub-items
- [x] 9.4 Register `rrhh.php` route in bootstrap/app.php or main route file
- [x] 9.5 Create `app/Http/Controllers/Rrhh/` directory structure

## 10. Tests

- [ ] 10.1 Create factory for WorkSchedule
- [ ] 10.2 Create factory for TimeOff
- [ ] 10.3 Create factory for CompanyHoliday
- [ ] 10.4 Write tests for Employee CRUD
- [ ] 10.5 Write tests for WorkSchedule save and view
- [ ] 10.6 Write tests for Clock in/out workflow
- [ ] 10.7 Write tests for TimeOff request and approval workflow
- [ ] 10.8 Write tests for CompanyHoliday CRUD
- [ ] 10.9 Write tests for HR settings
- [ ] 10.10 Run full test suite to verify no regressions
