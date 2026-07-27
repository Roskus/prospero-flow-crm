## ADDED Requirements

### Requirement: Set renewal reminder date
The system SHALL allow users to specify a Renewal Reminder Date on contracts. This date determines when the system notifies the Contract Manager that a contract is approaching renewal.

#### Scenario: User sets renewal reminder date
- **WHEN** a user creates or updates a contract and specifies a Renewal Reminder Date
- **THEN** the date is saved and stored on the contract

#### Scenario: Renewal reminder date is optional
- **WHEN** a user creates a contract without specifying a Renewal Reminder Date
- **THEN** the contract is created successfully with no renewal reminder configured

#### Scenario: User updates renewal reminder date
- **WHEN** a user edits a contract's Renewal Reminder Date
- **THEN** the new date is saved and any previous reminder scheduling is updated

### Requirement: Automatic renewal reminder notifications
The system SHALL execute a daily scheduled command that checks for contracts with Renewal Reminder Dates equal to or before today, and sends notifications to the assigned Contract Manager.

#### Scenario: Renewal reminder notification is sent
- **WHEN** the daily renewal reminder command runs and a contract's Renewal Reminder Date matches today's date
- **THEN** a notification is sent to the Contract Manager via email (and in-app if available)

#### Scenario: Notification includes contract details
- **WHEN** a renewal reminder notification is sent
- **THEN** the notification includes the contract title, account name, renewal reminder date, and a link to the contract detail page

#### Scenario: Contract manager receives notification if assigned
- **WHEN** a contract has a Renewal Reminder Date today and a Contract Manager assigned
- **THEN** the assigned Contract Manager receives the notification

#### Scenario: No notification sent if no contract manager
- **WHEN** a contract's Renewal Reminder Date is today but no Contract Manager is assigned
- **THEN** no notification is sent (or notification is sent to account owner or default recipient)

### Requirement: Track renewal history
The system SHALL track when contracts are renewed or extended, allowing users to view and update contract status to "Renewed" with a corresponding new End Date.

#### Scenario: User marks contract as renewed
- **WHEN** a user changes a contract's Status to "Renewed" and updates the End Date
- **THEN** the contract status is updated and the renewal is recorded

#### Scenario: Renewal reminder is cleared after renewal
- **WHEN** a user renews a contract by setting Status to "Renewed" and updating the Renewal Reminder Date
- **THEN** the old reminder is cleared and a new one can be set for the renewed contract

#### Scenario: Contract renewal is visible on contract detail
- **WHEN** a user views a renewed contract
- **THEN** the status "Renewed" is displayed along with the new End Date

### Requirement: Filter contracts by renewal status
The system SHALL allow users to filter the contract list by contracts approaching renewal (Renewal Reminder Date within X days) or recently renewed.

#### Scenario: User filters contracts approaching renewal
- **WHEN** a user selects "Approaching Renewal" filter
- **THEN** only contracts with a Renewal Reminder Date within the next 30 days are displayed

#### Scenario: User filters recently renewed contracts
- **WHEN** a user selects "Recently Renewed" filter
- **THEN** only contracts with Status "Renewed" updated in the last 30 days are displayed