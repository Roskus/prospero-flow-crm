## ADDED Requirements

### Requirement: Employee directory
The system SHALL provide an employee directory listing all users where `is_employee` is true, with search and pagination.

#### Scenario: List all employees
- **WHEN** user with `read rrhh` permission visits `/rrhh`
- **THEN** system displays a paginated table of employees (employee_number, name, email, phone, department, status)

#### Scenario: Search employees
- **WHEN** user searches by name, email, or employee_number
- **THEN** system filters the employee list matching the query

### Requirement: Employee profile
The system SHALL provide a detailed employee view showing personal info, schedule, work hours, time off balance, and payroll history.

#### Scenario: View employee profile
- **WHEN** user clicks on an employee in the directory
- **THEN** system shows the employee profile with all HR details including upcoming time off, recent hours, and payroll records

### Requirement: Employee CRUD
Authorized users SHALL be able to create, update, and delete employees (users with `is_employee = true`).

#### Scenario: Create employee
- **WHEN** user with `create rrhh` permission submits the employee form
- **THEN** system creates a new user with `is_employee = true` and the provided `employee_number`

#### Scenario: Update employee
- **WHEN** authorized user submits employee edit form
- **THEN** system updates the employee record

#### Scenario: Delete employee
- **WHEN** authorized user confirms deletion
- **THEN** system soft-deletes the user

### Requirement: Employee number uniqueness
The `employee_number` SHALL be unique per company.

#### Scenario: Duplicate employee number
- **WHEN** creating an employee with an existing employee_number in the same company
- **THEN** system returns a validation error
