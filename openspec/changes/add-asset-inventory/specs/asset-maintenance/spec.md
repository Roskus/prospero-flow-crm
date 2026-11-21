# Spec Delta

## Purpose

Records the maintenance performed on company assets — services, repairs and inspections — with its cost and the next due date, so upkeep can be planned and its cost known.

## ADDED Requirements

### Requirement: Log maintenance
A user with `update asset` permission SHALL be able to add a maintenance record to an asset with type (preventive, corrective, inspection), date, description, and optionally cost with currency, supplier, odometer/usage hours at the time, and next due date.

#### Scenario: Log a service
- **WHEN** a user logs a preventive service on a van dated today with cost 250 EUR and next due date in 6 months
- **THEN** the record appears in the asset maintenance history

#### Scenario: Missing date or type
- **WHEN** a user saves a maintenance record without date or type
- **THEN** the save is rejected with a validation error on the missing field

#### Scenario: Next due date before date
- **WHEN** a user saves a record whose next due date is earlier than its date
- **THEN** the save is rejected with a validation error on the next due date

### Requirement: Maintenance updates usage reading
When a maintenance record on a vehicle includes an odometer or usage hours reading greater than the one stored on the asset, the asset reading SHALL be updated to that value.

#### Scenario: Higher reading
- **WHEN** a maintenance record with odometer 85000 km is saved for a car whose odometer is 80000 km
- **THEN** the car odometer becomes 85000 km

#### Scenario: Lower reading
- **WHEN** a maintenance record with odometer 70000 km is saved for a car whose odometer is 80000 km
- **THEN** the car odometer stays 80000 km

### Requirement: Maintenance history
The asset detail SHALL list its maintenance records newest first and show the total maintenance cost per currency.

#### Scenario: View history and total
- **WHEN** an asset has records of 100 EUR and 150 EUR
- **THEN** the detail lists both and shows a total of 250 EUR

### Requirement: Next maintenance date
The asset's next maintenance date SHALL be the next due date of its most recent maintenance record, and SHALL feed the expiry indicators of the inventory.

#### Scenario: Next maintenance expiring soon
- **WHEN** the latest record of an asset has a next due date in 15 days
- **THEN** the asset is flagged as "expiring soon" in the listing

### Requirement: Put asset in maintenance
A user with `update asset` permission SHALL be able to set a non-retired, non-assigned asset to `in_maintenance` and back to `available`.

#### Scenario: Send to workshop
- **WHEN** a user sets an available crane to `in_maintenance`
- **THEN** the crane shows status `in_maintenance` and cannot be assigned

### Requirement: Edit and delete maintenance records
A user with `update asset` permission SHALL be able to edit and delete maintenance records of assets of their company.

#### Scenario: Delete record
- **WHEN** a user deletes a maintenance record
- **THEN** it no longer appears in the history and the totals are recalculated
