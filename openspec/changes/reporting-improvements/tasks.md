# Tasks

## 1. Reports hub

- [ ] 1.1 Rewrite `report/index` with `$reportGroups` (Sales: sales report, annual sales report; People: work hours report; Communication: email report) using the settings tile layout, descriptions and permission based visibility; verify with feature tests for an admin, a seller without hours permission, an employee with only `manage own timesheet` and a hidden empty group

## 2. Shared hours calculation

- [ ] 2.1 Create `WorkHourService::dailyHours()` with the worked hours, 6 hour break rule, expected hours from the schedule, holidays and approved time off; verify with unit-style feature tests for each rule (8 h without break → 7, holiday → 0 expected, approved time off → 0 expected, open entry adds nothing)
- [ ] 2.2 Make `TimeEntryIndexController` use the service and limit `user_id` to employees of the active company (404 otherwise) and to the own user without `read rrhh`; verify with existing time entry tests plus tests for another company's user and an employee requesting a colleague

## 3. Work hours report

- [ ] 3.1 Add `read hours report` to `PermissionSeeder` (SuperAdmin, CompanyAdmin, HR), `ReportHoursRequest` (month or from/to, max 366 days, employee filter forced to self without the permission) and `WorkHourService::summary()` loading data with one query per table; verify with tests for the default month, overtime, invalid ranges and scoping
- [ ] 3.2 Create the report page (filters, rows, totals, overtime and deficit highlighting) and the CSV export with the same data; verify with feature tests for the page, the employee only view, another company's employee, forbidden access and the CSV content
- [ ] 3.3 Verify with a test that the time entries week and the report for the same week show the same totals

## 4. Annual sales report

- [ ] 4.1 Add `Order::getAnnualSales()` (monthly sales and orders of the year and the previous year, year to date for the current year, changes) and the rankings (top 10 products with quantity, top 10 customers with orders, sales by seller) joined by `order_id` and scoped by company; verify with tests for the monthly comparison, excluded statuses, foreign currency, year to date, other companies, deleted products and the seller scope
- [ ] 4.2 Create `ReportAnnualSaleRequest`, the page with the year selector, the totals, the Chart.js bar chart (pinned CDN with SRI, data in a JSON script block) and the monthly table, and the rankings; verify with feature tests for the rendered table, the JSON chart data and the seller view
- [ ] 4.3 Create the PDF export with the same tables; verify with a render test and a download test

## 5. Translations and checks

- [ ] 5.1 Add every new key to `lang/en.json` and `lang/es.json`; verify with a key coverage test over the report views, controllers and requests and a Spanish rendering test
- [ ] 5.2 Run the full suite, `vendor/bin/pint --dirty --format agent`, Larastan on the changed files and `openspec validate reporting-improvements --strict`; verify everything passes
