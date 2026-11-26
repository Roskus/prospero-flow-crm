## MODIFIED Requirements

### Requirement: Asset categories and types
Every asset SHALL belong to exactly one category, `vehicle` (vehicles and machinery) or `device`, and to one type valid for that category. Vehicle types: car, van, truck, motorcycle, crane, lift, forklift, excavator, generator, compressor, other machinery. Device types: desktop, notebook, server, mobile, tablet, printer, router, other. Crane, lift, forklift, excavator, generator, compressor and other machinery SHALL be machinery types.

#### Scenario: Valid type for the category
- **WHEN** an asset of the vehicle category is saved with the type `generator`
- **THEN** it is accepted as a machinery type

## ADDED Requirements

### Requirement: Machinery data
A machinery asset SHALL hold, on top of its vehicle data, an optional capacity, certification number, certifying body, and certification, calibration and safety inspection expiry dates. Non-machinery assets SHALL have no machinery data; changing a machine to a non-machinery type SHALL remove it. The three expiry dates SHALL count as due dates for the expiry indicators and the "expiring" filter.

#### Scenario: Expired certification
- **WHEN** a crane's certification expired yesterday
- **THEN** the crane is flagged as expired and listed by the "expiring" filter

#### Scenario: Type changed to a vehicle
- **WHEN** a forklift is edited and saved as a truck
- **THEN** its machinery data is removed
