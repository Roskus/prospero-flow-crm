## ADDED Requirements

### Requirement: Link contracts to customers
The system SHALL allow contracts to be associated with a Customer. Each contract MUST have exactly one Customer (required field). Each Customer can have multiple Contracts (1:Many relationship). When a Customer is deleted, its associated contracts SHALL be handled according to database constraints (cascade or prevent deletion).

#### Scenario: User selects a customer when creating a contract
- **WHEN** a user creates a contract and selects a Customer from a dropdown
- **THEN** the contract is linked to that Customer and saved

#### Scenario: User views contracts associated with a customer
- **WHEN** a user views a Customer detail page
- **THEN** all contracts linked to that Customer are displayed

#### Scenario: User changes a customer association
- **WHEN** a user updates a contract's Customer field to a different Customer
- **THEN** the contract is re-linked to the new Customer

### Requirement: Link contracts to contacts
The system SHALL allow contracts to be optionally associated with a Contact. A contract MAY be linked to at most one Contact. Contact linkage is optional and independent of Account linkage.

#### Scenario: User optionally selects a contact when creating a contract
- **WHEN** a user creates a contract and optionally selects a Contact from a dropdown
- **THEN** the contract is linked to that Contact (if selected)

#### Scenario: User creates a contract without selecting a contact
- **WHEN** a user creates a contract and leaves the Contact field empty
- **THEN** the contract is created successfully with no Contact linkage

#### Scenario: User views contracts associated with a contact
- **WHEN** a user views a Contact detail page
- **THEN** all contracts linked to that Contact are displayed

### Requirement: Link contracts to opportunities
The system SHALL allow contracts to be optionally associated with an Opportunity. A contract MAY be linked to at most one Opportunity. Opportunity linkage is optional and allows tracking contract origin from sales pipeline.

#### Scenario: User optionally selects an opportunity when creating a contract
- **WHEN** a user creates a contract and optionally selects an Opportunity from a dropdown
- **THEN** the contract is linked to that Opportunity (if selected)

#### Scenario: User converts an opportunity to a contract by linking
- **WHEN** a user updates a contract to link it to a previously unlinked Opportunity
- **THEN** the contract is linked to that Opportunity and the relationship is displayed on the Opportunity detail page

#### Scenario: User views contracts associated with an opportunity
- **WHEN** a user views an Opportunity detail page
- **THEN** all contracts linked to that Opportunity are displayed

### Requirement: Assign a contract manager
The system SHALL allow contracts to be assigned to a User (Contract Manager). Each contract MAY have exactly one Contract Manager. The Contract Manager is responsible for the contract and receives renewal notifications.

#### Scenario: User assigns themselves as contract manager
- **WHEN** a user creates or updates a contract and selects themselves as the Contract Manager
- **THEN** the contract is linked to that User and saved

#### Scenario: User reassigns contract manager
- **WHEN** a user updates a contract's Contract Manager to a different User
- **THEN** the contract manager is changed and the new manager is notified of the assignment

#### Scenario: User views their assigned contracts
- **WHEN** a user views a "My Contracts" or "Assigned to Me" page
- **THEN** only contracts where they are the Contract Manager are displayed

### Requirement: Display relationships on detail pages
The system SHALL display all contract relationships (Account, Contact, Opportunity, Contract Manager) on the contract detail page with links to the related records.

#### Scenario: User views contract with all relationships populated
- **WHEN** a user views a contract detail page
- **THEN** all relationships are displayed as clickable links to the related Accounts, Contacts, Opportunities, or Users

#### Scenario: User views contract with partial relationships
- **WHEN** a user views a contract detail page that has no linked Contact or Opportunity
- **THEN** optional fields are shown as empty or "Not assigned" but required fields (Account) are always displayed
