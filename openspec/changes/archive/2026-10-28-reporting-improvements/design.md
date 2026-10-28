# Design

## Context

- `report/index` renders two cards; `setting/index` builds `$settingGroups` (title, icon, items with `visible`) and renders them as cards of `setting-tile` links. The reports hub reuses that structure.
- `ReportSaleRequest::reportData()` builds the period sales report from `Order::getSalesReport()`; sales are CONFIRMED and COMPLETED orders by `order_date`, summed as `amount * exchange_rate`. `canSeeAllSellers()` is true for SuperAdmin and company admins; other users get their own `seller_id`.
- Hours live in `work_hour (user_id, start_time, end_time, type work|break, is_manual)`; there is no `company_id`. Employees are `user` rows with `company_id` and `is_employee`. Schedules are `work_schedule (user_id, day_of_week, start_time, end_time, type)`, holidays `company_holiday (company_id, date)`, time off `time_off (company_id, user_id, start_date, end_date, status approved|pending|rejected)`.
- `TimeEntryIndexController` computes the daily totals inline (6 hour break rule) and reads `user_id` from the request without checking the company.
- `CsvExportService::download()` streams CSV with cell sanitizing. Sales PDFs use dompdf (`ReportSalePdfController`).
- No chart library is loaded today.

## Goals / Non-Goals

**Goals:**
- One source of truth for daily hours, used by the time entries screen and the report.
- Report queries bounded by period and company, aggregated in SQL where possible (annual sales) or with one query per table for the period (hours).

**Non-Goals:**
- Charts in PDFs (dompdf runs no JavaScript); PDFs show tables.
- Overtime approval, payroll export or per branch hours.
- Changing the existing period sales report or the email report.

## Decisions

### Reports hub as data, like settings
`ReportIndexController` passes nothing new; the view builds `$reportGroups` with `title`, `icon` and items (`url`, `icon`, `label`, `description`, `visible`) exactly like `$settingGroups`, and reuses the `setting-tile` markup. Visibility uses the same permissions as the routes, so a tile is never shown for a forbidden report.
- Alternative: a shared Blade component for both screens. Deferred; two call sites do not justify it yet, and it would touch settings.

### `WorkHourService`
- `dailyHours(User $employee, Carbon $from, Carbon $to): Collection<string, array{date, worked, gross, expected, is_holiday, holiday_name, entries}>` loads, once per call, the entries, schedule blocks, holidays of the employee company and approved time off for the range and applies the rules of the spec.
- `summary(Collection $employees, Carbon $from, Carbon $to): list<array{employee, worked, expected, difference, days_worked, open_entries}>` for the report. It loads entries, schedules and time off for all the employees with `whereIn('user_id', ...)` (one query per table), then reuses the same per day rules, so the report does not run per employee queries.
- `TimeEntryIndexController` calls `dailyHours()` for its week and keeps its view data shape.
- Hours are floats rounded to 2 decimals at display; minutes are summed as integers to avoid drift.

### Company scope for hours
Employees are `User::where('company_id', active)->where('is_employee', true)`; entries are read only `whereIn('user_id', those ids)`. A requested `user_id` outside that set returns 404 in time entries and is ignored in the report. Users without `read rrhh` / `read hours report` are forced to their own id.

### Annual sales queries on `Order`
- `getAnnualSales(int $companyId, int $year, ?int $sellerId): array` runs one grouped query per year: `SELECT MONTH(order_date), SUM(amount * exchange_rate), COUNT(*) ... GROUP BY MONTH(order_date)` with the sales status filter, and fills missing months with zero.
- Year to date for the current year: the previous year total is limited to the same month and day.
- Rankings use `order_item` joined to `order` by `order_id` (never by `order_number`) with `order.company_id`, summing `unit_price * quantity * (1 - discount/100) * order.exchange_rate`; products and customers are joined `withTrashed`/left join so deleted ones keep their name.
- `MONTH()` is available in MySQL and MariaDB, the supported databases.

### Charts with Chart.js from CDN
- Chart.js 4.4.x from `cdn.jsdelivr.net`, pinned with an SRI hash, included through a `@push('scripts')` stack only on the annual sales page. The data goes into the page as a JSON `<script type="application/json">` block read by a small inline script, so no user data is concatenated into JavaScript.
- Colors: the year in the primary color and the previous year in a muted grey; the table is always rendered, so the page works if the CDN is blocked.
- Alternative: hand drawn SVG bars in Blade. Rejected: no tooltips or responsive axes, more code to maintain.
- Alternative: npm/Vite bundle. Rejected: the layout loads every library from CDNs today.
- Approved as a new front-end dependency (Chart.js 4.4.1).

### Routes and permissions
- `/report/hours` and `/report/hours/csv` use `permission:read hours report|manage own timesheet`; the request decides the scope.
- `/report/sale/annual` and `/report/sale/annual/pdf` use `can('read sales report')`, with the seller scope from `ReportSaleRequest::canSeeAllSellers()` (shared through a trait or the request base class).
- `read hours report` is added to `PermissionSeeder` for SuperAdmin, CompanyAdmin and HR.

## Risks / Trade-offs

- [`work_hour` has no `company_id`, so an employee moved to another company brings their past entries] → The report reads entries of the current employees of the company only; adding `company_id` to `work_hour` is left for a later change.
- [Large companies and long ranges] → The range is capped at 366 days and data is loaded with one query per table; the annual sales report is fully aggregated in SQL.
- [CDN blocked or offline] → The table below the chart carries the same figures.
- [The 6 hour break rule is hard coded] → It stays as today, moved into the service; making it configurable per company is a later change.

## Migration Plan

1. Deploy the code; no migrations.
2. Run `php artisan db:seed --class=PermissionSeeder` to create `read hours report`.

Rollback: revert the code; the extra permission is harmless.
