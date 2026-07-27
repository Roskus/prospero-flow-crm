## ADDED Requirements

### Requirement: Track customer signed date
The system SHALL allow users to record the date when a customer (Contact) signed the contract. This date is optional and tracked separately from the contract creation date.

#### Scenario: User enters customer signed date
- **WHEN** a user creates or updates a contract and enters a Customer Signed Date
- **THEN** the date is saved and displayed on the contract detail page

#### Scenario: Customer signed date is optional
- **WHEN** a user creates a contract without entering a Customer Signed Date
- **THEN** the contract is created successfully with no Customer Signed Date

#### Scenario: User updates customer signed date
- **WHEN** a user edits a contract's Customer Signed Date
- **THEN** the new date is saved and persisted

### Requirement: Track company signed date
The system SHALL allow users to record the date when the company (represented by a Company Signer, typically the Contract Manager or designated user) signed the contract.

#### Scenario: User enters company signed date
- **WHEN** a user creates or updates a contract and enters a Company Signed Date
- **THEN** the date is saved and displayed on the contract detail page

#### Scenario: Company signed date is optional
- **WHEN** a user creates a contract without entering a Company Signed Date
- **THEN** the contract is created successfully with no Company Signed Date

#### Scenario: User updates company signed date
- **WHEN** a user edits a contract's Company Signed Date
- **THEN** the new date is saved and persisted

### Requirement: Display contract signing status
The system SHALL display a contract's signing status based on whether Customer Signed Date and Company Signed Date are populated.

#### Scenario: Contract signing status fully signed
- **WHEN** a user views a contract with both Customer Signed Date and Company Signed Date populated
- **THEN** a "Fully Signed" badge or status is displayed

#### Scenario: Contract signing status partially signed
- **WHEN** a user views a contract with only one of Customer Signed Date or Company Signed Date populated
- **THEN** a "Partially Signed" badge or status is displayed

#### Scenario: Contract signing status not signed
- **WHEN** a user views a contract with neither Customer Signed Date nor Company Signed Date populated
- **THEN** a "Not Signed" badge or status is displayed

### Requirement: Filter contracts by signing status
The system SHALL allow users to filter the contract list by signing status (Signed, Partially Signed, Not Signed).

#### Scenario: User filters for fully signed contracts
- **WHEN** a user selects "Fully Signed" filter
- **THEN** only contracts with both Customer Signed Date and Company Signed Date populated are displayed

#### Scenario: User filters for unsigned contracts
- **WHEN** a user selects "Not Signed" filter
- **THEN** only contracts with neither Customer Signed Date nor Company Signed Date populated are displayed

### Requirement: Track signing workflow timeline
The system SHALL display signing dates on the contract detail page in a clear timeline or section, showing the sequence of customer and company signatures.

#### Scenario: User views signing timeline
- **WHEN** a user views a contract detail page
- **THEN** Customer Signed Date and Company Signed Date are displayed prominently (in signing status section or timeline)

#### Scenario: Signing dates inform contract status
- **WHEN** a contract's Status is "Active" and both signing dates are present
- **THEN** the contract is considered valid and all related features (renewals, financials) apply
