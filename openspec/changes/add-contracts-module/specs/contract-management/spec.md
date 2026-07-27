## ADDED Requirements

### Requirement: Create a new contract
The system SHALL allow users with appropriate permissions to create a new contract with required fields: Contract Title, Status, and Customer. Optional fields include Contact, Opportunity, Contract Type, Description, Contract Value, Currency, Contract Manager, Start Date, End Date, and Renewal Reminder Date.

#### Scenario: User creates a contract with required fields
- **WHEN** a user submits a contract creation form with Title, Status, and Customer populated
- **THEN** the contract is saved to the database with a unique ID and default values for optional fields

#### Scenario: User attempts to create a contract without required fields
- **WHEN** a user submits a contract creation form missing a required field (Title, Status, or Customer)
- **THEN** the system displays validation errors and does not save the contract

### Requirement: View contract details
The system SHALL display detailed information for a specific contract, including all fields (title, status, dates, financial values, relationships, etc.).

#### Scenario: User views an existing contract
- **WHEN** a user navigates to a contract detail page
- **THEN** all contract fields are displayed with accurate current data

#### Scenario: User attempts to view a contract without permission
- **WHEN** a user without view permission accesses a contract detail page
- **THEN** the system returns a 403 Forbidden response

### Requirement: List contracts with filtering and pagination
The system SHALL display a paginated list of all contracts accessible to the current user, with options to filter by status, account, date range, and contract manager.

#### Scenario: User views contracts list
- **WHEN** a user navigates to the contracts listing page
- **THEN** all contracts are displayed in a paginated table with 15 contracts per page

#### Scenario: User filters contracts by status
- **WHEN** a user selects a status filter (e.g., "Active") and applies it
- **THEN** only contracts with that status are displayed

#### Scenario: User searches contracts by title
- **WHEN** a user enters search text in the contract title field
- **THEN** only contracts with matching titles are displayed

### Requirement: Update contract details
The system SHALL allow users with appropriate permissions to update any contract field except the contract ID.

#### Scenario: User updates contract status
- **WHEN** a user changes a contract's status and saves the form
- **THEN** the contract's status is updated in the database

#### Scenario: User updates multiple fields
- **WHEN** a user modifies title, description, and dates simultaneously and saves
- **THEN** all modified fields are persisted to the database

### Requirement: Delete a contract
The system SHALL allow users with appropriate permissions to delete a contract, removing all associated line items.

#### Scenario: User deletes a contract
- **WHEN** a user confirms deletion of a contract
- **THEN** the contract and all its line items are removed from the database

#### Scenario: User attempts to delete a contract without permission
- **WHEN** a user without delete permission attempts to delete a contract
- **THEN** the system returns a 403 Forbidden response and the contract remains intact

### Requirement: Contract authorization and access control
The system SHALL enforce authorization policies such that users can only view, edit, or delete contracts within their company scope.

#### Scenario: User views only company-scoped contracts
- **WHEN** a user lists contracts
- **THEN** only contracts belonging to the user's company are displayed

#### Scenario: User with admin role can view all contracts in company
- **WHEN** an admin user lists contracts
- **THEN** all contracts in the company are displayed regardless of which user created them
