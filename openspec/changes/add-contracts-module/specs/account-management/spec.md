## MODIFIED Requirements

### Requirement: View contracts associated with a customer
The system SHALL display all contracts linked to a specific Customer on the Customer detail page in a contracts section or related items panel.

#### Scenario: User views a customer with associated contracts
- **WHEN** a user navigates to a Customer detail page
- **THEN** all contracts linked to that Customer are displayed in a contracts list or table

#### Scenario: User views a customer with no contracts
- **WHEN** a user navigates to a Customer detail page with no associated contracts
- **THEN** a message indicating "No contracts" or an empty state is displayed

#### Scenario: User navigates to a contract from customer detail
- **WHEN** a user clicks on a contract link in the Customer detail page
- **THEN** the user is navigated to the contract detail page

### Requirement: Filter customer contracts by status
The system SHALL allow users to filter contracts displayed on a Customer detail page by contract status (Draft, Active, Expired, Renewed, Cancelled).

#### Scenario: User filters customer contracts by status
- **WHEN** a user selects a status filter on the Customer detail contracts section
- **THEN** only contracts with the selected status are displayed

### Requirement: Quick contract actions from customer detail
The system SHALL provide quick-action buttons on the Customer detail page to create a new contract or view all customer contracts.

#### Scenario: User creates a new contract from customer detail
- **WHEN** a user clicks "Create Contract" button on a Customer detail page
- **THEN** the contract creation form is opened with the Customer pre-selected
