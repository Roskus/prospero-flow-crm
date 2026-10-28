# work-hours-report Specification

## Purpose
Shows, for a period, how many hours each employee of the active company worked against the hours expected by their work schedule, so managers and HR can review attendance and overtime without opening each employee week by week.

## Requirements

### Requirement: Daily worked and expected hours
The worked hours of a day SHALL be the sum of the closed work and break entries that start that day. When a day has more than 6 worked hours and no break entry, 1 hour SHALL be deducted. The expected hours of a day SHALL be the sum of the work blocks of the employee schedule for that weekday, with 1 hour deducted when they exceed 6 hours and the schedule has no break; expected hours SHALL be 0 on company holidays and on days covered by approved time off. The time entries screen and the work hours report SHALL use these same rules and SHALL show the same totals for the same employee and days.

#### Scenario: Long day without a break
- **WHEN** an employee has one closed entry of 8 hours on a day and no break entry
- **THEN** the worked hours of that day are 7

#### Scenario: Holiday
- **WHEN** a day is a holiday of the company and the employee schedule expects 8 hours that weekday
- **THEN** the expected hours of that day are 0

#### Scenario: Approved time off
- **WHEN** an employee has approved time off covering a day
- **THEN** the expected hours of that day are 0

#### Scenario: Same numbers in both screens
- **WHEN** a user compares the week of an employee in the time entries screen and in the work hours report for that week
- **THEN** both show the same worked and expected hours

### Requirement: Work hours report
The work hours report SHALL show, for a period (default the current month), one row per employee of the active company with worked hours, expected hours, the difference (worked minus expected), days with worked hours and the number of entries still open, followed by a totals row. Hours SHALL be shown with two decimals. The period SHALL be chosen as a month or as a custom from/to range of at most 366 days, with the end date not earlier than the start date. Open entries SHALL not add hours.

#### Scenario: Month by default
- **WHEN** a user with `read hours report` opens the report without filters on 15/10/2026
- **THEN** the period is 01/10/2026 to 31/10/2026
- **AND** each employee of the active company has a row

#### Scenario: Overtime
- **WHEN** an employee worked 170 hours in a month where 160 were expected
- **THEN** the row shows a difference of +10.00 highlighted as overtime

#### Scenario: Invalid range
- **WHEN** a user requests a range ending before it starts or longer than 366 days
- **THEN** the report is not computed and a validation error is shown

#### Scenario: Open clock-in
- **WHEN** an employee clocked in and has not clocked out
- **THEN** the row shows 1 open entry and the entry adds no worked hours

### Requirement: Work hours report scope and permissions
The report SHALL only include employees of the active company and SHALL only read their entries. Users with `read hours report` SHALL see every employee and MAY filter by one employee. Users with `manage own timesheet` and without `read hours report` SHALL see only their own row and SHALL NOT be able to choose another employee. Other users SHALL be rejected with a forbidden error.

#### Scenario: Employee of another company
- **WHEN** a user filters the report by an employee of another company
- **THEN** that employee is not shown and no hours of the other company are read

#### Scenario: Employee sees only their own hours
- **WHEN** a user with only `manage own timesheet` requests the report filtered by another employee
- **THEN** the report shows only the user's own row

#### Scenario: User without permission
- **WHEN** a user without `read hours report` and without `manage own timesheet` opens the report
- **THEN** the request is rejected with a forbidden error

### Requirement: Work hours CSV export
The report SHALL be exportable to CSV with the same period, filters, rows and totals shown on screen, using the user's permissions in the same way.

#### Scenario: Export
- **WHEN** a user exports the report of October 2026
- **THEN** a CSV file with one line per employee row and a totals line is downloaded

### Requirement: Time entries scoped to the active company
The time entries screen SHALL only show entries of employees of the active company. Users without `read rrhh` SHALL only see their own entries, whatever employee they request.

#### Scenario: Requesting another company's employee
- **WHEN** a user requests the time entries of a user of another company
- **THEN** the request returns not found

#### Scenario: Employee requesting a colleague
- **WHEN** a user with only `manage own timesheet` requests the time entries of a colleague
- **THEN** the screen shows the user's own entries
