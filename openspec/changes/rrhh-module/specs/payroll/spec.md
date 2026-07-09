## ADDED Requirements

### Requirement: Payroll records
The system SHALL maintain payroll records per employee with amount, payment date, file attachment, and notes.

#### Scenario: Create payroll entry
- **WHEN** authorized user creates a payroll entry for an employee with amount, payment_date, and optional file
- **THEN** system saves the payroll record

#### Scenario: List payroll entries
- **WHEN** authorized user views payroll list filtered by year
- **THEN** system displays payroll records with employee name, amount, and payment date

### Requirement: Link payroll to employees
Payroll entries SHALL be linked to the User model and displayable in the employee profile.

#### Scenario: View payroll in employee profile
- **WHEN** viewing an employee profile
- **THEN** system shows their payroll history if any
