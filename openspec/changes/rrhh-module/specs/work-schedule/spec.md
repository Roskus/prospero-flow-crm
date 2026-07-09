## ADDED Requirements

### Requirement: Define weekly schedule
Authorized users SHALL be able to define an employee's weekly work schedule with configurable hours per day of week.

#### Scenario: Set full-time schedule
- **WHEN** authorized user sets Monday-Thursday 9:00-13:00 + 14:00-17:30 and Friday 9:00-14:00
- **THEN** system saves the schedule with split shifts for Mon-Thu and a single shift for Fri

#### Scenario: Set half-time schedule
- **WHEN** authorized user sets Monday-Friday 9:00-13:00
- **THEN** system saves a single shift per day

### Requirement: Split shift support
The system SHALL support multiple time blocks per day (e.g., morning + afternoon with a break in between).

#### Scenario: Save split shift
- **WHEN** authorized user adds two work blocks for the same day (e.g., 9-13 type:work, 13-14 type:break, 14-17:30 type:work)
- **THEN** system saves all blocks for that day

### Requirement: View schedule
Employees SHALL be able to view their own work schedule.

#### Scenario: View my schedule
- **WHEN** employee visits `/rrhh/schedule`
- **THEN** system displays their weekly schedule in a readable format
