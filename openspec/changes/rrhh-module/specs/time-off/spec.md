## ADDED Requirements

### Requirement: Request time off
Employees SHALL be able to request time off (vacation, sick leave, or personal days).

#### Scenario: Submit vacation request
- **WHEN** employee submits a time off request with type, start_date, end_date, and reason
- **THEN** system creates a time_off entry with status = 'pending' and calculates days_used

#### Scenario: Request exceeds available days
- **WHEN** employee requests more vacation days than their remaining balance
- **THEN** system shows a validation error

### Requirement: Approval workflow
Time off requests SHALL require approval from the employee's manager AND a user with `approve timeoff` permission.

#### Scenario: Manager approves
- **WHEN** the employee's manager approves the request
- **THEN** system sets manager_approved_by and manager_approved_at

#### Scenario: RRHH approves after manager
- **WHEN** a user with `approve timeoff` permission approves a request that already has manager approval
- **THEN** system sets rrhh_approved_by, rrhh_approved_at, and status = 'approved'

#### Scenario: Request rejected
- **WHEN** manager or RRHH user rejects a request with a reason
- **THEN** system sets status = 'rejected' and stores the rejection reason

### Requirement: Vacation balance
The system SHALL calculate and display remaining vacation days based on the employee's annual allowance minus approved time off.

#### Scenario: View remaining days
- **WHEN** employee views their time off page
- **THEN** system displays total annual days, used days, and remaining balance

### Requirement: Time off calendar
Authorized users SHALL see all approved time off on a calendar view.

#### Scenario: View team calendar
- **WHEN** authorized user visits the time off calendar
- **THEN** system shows all approved time off for their team/department
