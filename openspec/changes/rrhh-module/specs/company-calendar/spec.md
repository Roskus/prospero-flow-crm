## ADDED Requirements

### Requirement: Manage company holidays
Authorized users SHALL be able to create, list, and delete company-wide non-working days.

#### Scenario: Add holiday
- **WHEN** authorized user adds a date with a name (e.g., "Christmas")
- **THEN** system creates a company_holiday entry for that date

#### Scenario: Delete holiday
- **WHEN** authorized user deletes a holiday
- **THEN** system removes that holiday entry

### Requirement: Display holiday calendar
The system SHALL display company holidays in a calendar view for all employees.

#### Scenario: View holidays
- **WHEN** any employee visits the calendar
- **THEN** system shows all company holidays for the current and upcoming months

### Requirement: Holiday validation
The system SHALL prevent duplicate holidays on the same date for the same company.

#### Scenario: Duplicate date
- **WHEN** adding a holiday on a date that already has one
- **THEN** system shows a validation error
