## ADDED Requirements

### Requirement: Company HR configuration
The system SHALL allow configuring HR settings at the company level: default vacation days per year and full-time weekly hours.

#### Scenario: Configure default vacation days
- **WHEN** authorized user sets `vacation_days_per_year` to 22
- **THEN** all new employees default to 22 vacation days per year

#### Scenario: Configure full-time weekly hours
- **WHEN** authorized user sets `weekly_hours_full_time` to 37.0
- **THEN** the system uses 37h as the standard full-time baseline

### Requirement: Employee override
The system SHALL allow overriding HR settings at the individual employee level.

#### Scenario: Override vacation days per employee
- **WHEN** authorized user sets `vacation_days_override` to 25 for a specific employee
- **THEN** that employee's balance uses 25 days instead of the company default

#### Scenario: Override weekly hours per employee
- **WHEN** authorized user sets `weekly_hours_override` to 18.5 for a part-time employee
- **THEN** that employee's weekly hours default to 18.5

### Requirement: Read cascading settings
The system SHALL resolve an employee's effective vacation days and weekly hours by checking employee override first, then company default.

#### Scenario: Employee with override
- **WHEN** querying effective settings for an employee with `vacation_days_override` set
- **THEN** system returns the override value

#### Scenario: Employee without override
- **WHEN** querying effective settings for an employee without override
- **THEN** system falls back to the company default
