## ADDED Requirements

### Requirement: Clock in/out
Employees SHALL be able to clock in and clock out to record work hours in real time.

#### Scenario: Clock in
- **WHEN** employee clicks "Clock in" and confirms
- **THEN** system creates a work_hour entry with start_time = now() and end_time = null

#### Scenario: Clock out
- **WHEN** employee clicks "Clock out" and they have an open entry (end_time is null)
- **THEN** system sets end_time = now() on that entry

#### Scenario: Clock out without open entry
- **WHEN** employee clicks "Clock out" but has no open entry
- **THEN** system shows an error message

### Requirement: Manual time entry
Authorized users SHALL be able to add, edit, or delete manual time entries for any employee.

#### Scenario: Add manual entry
- **WHEN** authorized user submits a manual time entry with user, start_time, end_time, and optional notes
- **THEN** system creates a work_hour entry with is_manual = true

### Requirement: Time entry history
The system SHALL display a history of work hours with filters by employee, date range, and type.

#### Scenario: View my hours
- **WHEN** employee visits `/rrhh/time-entries`
- **THEN** system displays their work hour history for the current week with total hours calculated

#### Scenario: View employee hours (admin)
- **WHEN** authorized user views `/rrhh/time-entries` with employee filter
- **THEN** system displays the selected employee's work hours

### Requirement: Prevent overlapping entries
The system SHALL prevent clock in if the employee already has an open entry (end_time is null).

#### Scenario: Prevent double clock-in
- **WHEN** employee has an open time entry and tries to clock in again
- **THEN** system rejects the request and notifies that they must clock out first
