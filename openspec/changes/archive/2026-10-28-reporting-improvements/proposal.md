# Proposal

## Why

The reports screen is two plain cards with an icon and a link, while the settings screen already groups its entries into titled sections of tiles. Reporting also lacks what managers ask for most: how many hours each employee worked against their schedule, and how the year is going month by month compared with the previous one. Today the hours only show week by week per employee in the time entries screen, and the sales report only compares one period with the period before it, as numbers.

## What Changes

- **Reports hub**: the reports screen uses the same layout as settings: titled groups (Sales, People, Communication) of icon tiles, each with a short description. Only the reports the user can open are shown, and a group with no visible report is hidden.
- **Work hours report** (new): for a month (default the current month) or a custom date range, one row per employee of the active company with worked hours, expected hours from the work schedule (minus company holidays and approved time off), the difference, days worked and open clock-ins. It can be filtered by employee and exported to CSV. Users with `read hours report` see every employee; other users with `manage own timesheet` see only their own row.
- **Annual sales report** (new): for a year (default the current year), monthly sales of the year against the previous year in a bar chart and a table, the year total with its change, the top 10 products and top 10 customers of the year by amount, and the sales by seller. Figures are in the company currency (`amount × exchange_rate`), counting confirmed and completed orders by `order_date`, as the current sales report does. It can be exported to PDF (tables, without the chart).
- **Shared hours calculation**: the worked and expected hours of a day are computed by one service used by the time entries screen and the new report, so both always show the same numbers.
- **Security fix**: the time entries screen accepts a `user_id` of any user; it is limited to employees of the active company, and users without `read rrhh` can only see their own entries.
- **New permission** `read hours report` for SuperAdmin, CompanyAdmin and HR. The annual sales report uses the existing `read sales report`.
- **Charts**: Chart.js is loaded from the jsDelivr CDN (pinned version with SRI hash, like Bootstrap) only on report pages that draw charts. This adds a front-end dependency (approved).

## Capabilities

### New Capabilities

- `reports-hub`: the grouped reports screen and which reports each user sees.
- `work-hours-report`: worked versus expected hours per employee for a period, its filters, permissions and CSV export, and the shared daily hours rules.
- `annual-sales-report`: monthly sales of a year against the previous year, top products, top customers and sales by seller, with chart and PDF export.

### Modified Capabilities

None. The current period sales report and email report keep their behavior; only their entry on the reports screen changes.

## Impact

- **Views**: `report/index` rewritten; new `report/hours`, `report/annual_sale` and `report/annual_sale_pdf`; `rrhh/time_entry/index` reads the shared service.
- **Controllers / requests**: `ReportHoursController`, `ReportHoursCsvController`, `ReportAnnualSaleController`, `ReportAnnualSalePdfController`, `ReportHoursRequest`, `ReportAnnualSaleRequest`; `TimeEntryIndexController` scoping fix.
- **Models / services**: new `WorkHourService` (daily worked and expected hours); new annual sales queries on `Order`.
- **Routes**: `/report/hours`, `/report/hours/csv`, `/report/sale/annual`, `/report/sale/annual/pdf` in `web.php`.
- **Permissions**: `read hours report` in `PermissionSeeder` (existing installs run the seeder).
- **Dependencies**: Chart.js from CDN (approved).
- **Data**: no migrations. Hours come from `work_hour` joined to `user.company_id`, since `work_hour` has no `company_id`.
