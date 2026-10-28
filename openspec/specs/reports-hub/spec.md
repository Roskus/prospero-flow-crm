# reports-hub Specification

## Purpose
Gives users one place to find every report they can open, grouped by area and presented with the same tile layout as the settings screen.

## Requirements

### Requirement: Grouped reports screen
The reports screen SHALL show the reports in titled groups (Sales, People and Communication), each report as a tile with an icon, a name and a one line description that links to the report. The layout SHALL match the settings screen: groups as cards side by side on wide screens and stacked on narrow screens.

#### Scenario: Reports grouped by area
- **WHEN** a company administrator opens the reports screen
- **THEN** the Sales group shows the sales report and the annual sales report
- **AND** the People group shows the work hours report
- **AND** the Communication group shows the email report

#### Scenario: Narrow screen
- **WHEN** the reports screen is opened on a phone
- **THEN** the groups are stacked in one column and no horizontal scroll appears

### Requirement: Reports visible by permission
The reports screen SHALL show only the reports the user is allowed to open, and SHALL hide a group with no visible report. Opening a report without its permission SHALL be rejected with a forbidden error.

#### Scenario: Seller without hours permission
- **WHEN** a seller with the sales report permission and without the hours report permission opens the reports screen
- **THEN** the Sales group is shown
- **AND** the work hours report tile is not shown

#### Scenario: Employee who only manages their own timesheet
- **WHEN** a user with `manage own timesheet` and without `read hours report` opens the reports screen
- **THEN** the work hours report tile is shown

#### Scenario: Hidden empty group
- **WHEN** a user can open no report of the Sales group
- **THEN** the Sales group is not shown
