## Why

The CRM needs a Human Resources module to manage employees, track work hours, handle time off/vacations, and manage payroll — core operational needs for any company. Currently there are partial foundations (User model, basic Payroll CRUD, WorkHour table) but no cohesive HR system.

## What Changes

- Add `employee_number`, `is_employee` fields to the User model
- Create weekly work schedule management (per-employee, per-day-of-week, with split shift support)
- Implement clock-in/clock-out and manual time entry with history
- Add time off/vacation request and approval workflow (manager + RRHH role)
- Create company-level holiday calendar
- Add company-wide config for vacation days per year and full-time weekly hours
- Improve payroll module UI and add HR context
- Add new sidebar menu dropdown for RRHH with employees, schedules, clock, time off, holidays, and payroll
- Add granular permissions for each sub-module
- Update CustomerExport and LeadImport to also use dynamic column mapping

## Capabilities

### New Capabilities
- `employee-management`: Employee directory with extended profile (employee number, employee status, org hierarchy via manager_id)
- `work-schedule`: Weekly work schedule definition per employee with split shift support
- `time-tracking`: Clock in/out and manual time entry with work hour history
- `time-off`: Vacation, sick leave, and personal day requests with manager + RRHH approval workflow
- `company-calendar`: Company holiday calendar management
- `hr-settings`: Company-level HR configuration (vacation days per year, weekly hours)

### Modified Capabilities
- `payroll`: Improve existing payroll CRUD with better UI and employee context
- `user-management`: Extend user with employee-specific fields (employee_number, is_employee)

## Impact

- User model migration: add `employee_number`, `is_employee`, `vacation_days_override`, `weekly_hours_override`
- Company model migration: add `vacation_days_per_year`, `weekly_hours_full_time`
- New tables: `work_schedule`, `time_off`, `company_holiday`
- Extend existing `work_hour` table with `type`, `is_manual`, `notes` columns
- New route file: `routes/module/rrhh.php`
- New controllers under `app/Http/Controllers/Rrhh/` following single-responsibility pattern
- New models: `WorkSchedule`, `TimeOff`, `CompanyHoliday`
- New views under `resources/views/rrhh/`
- New permissions seeded via PermissionSeeder
- Menu update in `resources/views/menu/menu.php`
