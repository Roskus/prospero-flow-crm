## MODIFIED Requirements

### Requirement: User model has employee fields
The User model SHALL include `employee_number` (string, nullable, unique per company) and `is_employee` (boolean, default true).

#### Scenario: Create user with employee number
- **WHEN** creating a new user with `is_employee = true` and an `employee_number`
- **THEN** the user record includes both fields

#### Scenario: Non-employee user
- **WHEN** creating a user with `is_employee = false`
- **THEN** `employee_number` is not required and can be null
