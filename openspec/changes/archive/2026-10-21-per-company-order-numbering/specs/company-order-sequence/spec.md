## ADDED Requirements

### Requirement: Company tracks its own order sequence counter
The `company` table SHALL have a `last_order_number` field (unsigned integer, default 0) that stores the last order number issued for that company.

#### Scenario: Default counter value
- **WHEN** a company exists with no orders
- **THEN** its `last_order_number` SHALL be 0

#### Scenario: Counter reflects last issued order number
- **WHEN** a company has created N orders
- **THEN** `company.last_order_number` SHALL equal the `order_number` of the most recently created order

---

### Requirement: Automatic order number assignment on creation
The system SHALL automatically assign `order_number` to a new order by incrementing the owning company's `last_order_number` before the record is persisted. No caller SHALL provide `order_number` manually.

#### Scenario: New order receives incremented number
- **WHEN** a new `Order` is saved for a company whose `last_order_number` is N
- **THEN** the order's `order_number` SHALL be N + 1 and `company.last_order_number` SHALL be updated to N + 1

#### Scenario: Updating an existing order does not change its order number
- **WHEN** an existing order is updated
- **THEN** its `order_number` SHALL remain unchanged and `company.last_order_number` SHALL not be incremented

---

### Requirement: Per-company isolated order number sequence
Each company SHALL maintain an independent sequence. Orders belonging to different companies SHALL NOT share or influence each other's counters.

#### Scenario: Sequential numbering within a company
- **WHEN** a company creates multiple orders in succession
- **THEN** each order's `order_number` SHALL be exactly 1 greater than the previous order's `order_number`

#### Scenario: Independent sequences across companies
- **WHEN** two different companies each create their first order
- **THEN** both orders MAY have the same `order_number` value without conflict

---

### Requirement: Concurrency-safe increment
The `last_order_number` increment SHALL be atomic so that concurrent order creation for the same company never produces duplicate `order_number` values.

#### Scenario: Concurrent orders produce unique numbers
- **WHEN** two orders for the same company are saved concurrently
- **THEN** each order SHALL receive a distinct `order_number`

---

### Requirement: Existing order sequences are preserved on deploy
For companies that already have orders when the feature is deployed, the system SHALL seed `last_order_number` from their historical maximum so the sequence continues without gaps or collisions.

#### Scenario: Data migration backfills existing companies
- **WHEN** the migration runs
- **THEN** every company SHALL have `last_order_number` equal to the maximum `order_number` among its existing (non-deleted) orders, or 0 if it has none

#### Scenario: First new order after deploy continues the sequence
- **WHEN** an existing company creates a new order after deploy
- **THEN** the new order's `order_number` SHALL equal the previous maximum order number plus 1