## MODIFIED Requirements

### Requirement: View contracts associated with an opportunity
The system SHALL display all contracts linked to a specific Opportunity on the Opportunity detail page in a contracts section or related items panel.

#### Scenario: User views an opportunity with associated contracts
- **WHEN** a user navigates to an Opportunity detail page
- **THEN** all contracts linked to that Opportunity are displayed in a contracts list or table

#### Scenario: User views an opportunity with no contracts
- **WHEN** a user navigates to an Opportunity detail page with no associated contracts
- **THEN** a message indicating "No contracts" or an empty state is displayed

#### Scenario: User navigates to a contract from opportunity detail
- **WHEN** a user clicks on a contract link in the Opportunity detail page
- **THEN** the user is navigated to the contract detail page

### Requirement: Create contract from opportunity
The system SHALL provide a button on the Opportunity detail page to create a new contract with the Opportunity pre-linked and the Opportunity's Customer pre-selected.

#### Scenario: User creates a contract from opportunity detail
- **WHEN** a user clicks "Create Contract" button on an Opportunity detail page
- **THEN** the contract creation form is opened with the Opportunity pre-selected and the Customer from the Opportunity pre-filled

### Requirement: Track opportunity-to-contract conversion
The system SHALL track the relationship between Opportunities and Contracts to enable sales forecasting based on closed opportunities converted to active contracts.

#### Scenario: Opportunity linked to active contract
- **WHEN** an Opportunity is linked to a Contract with Status "Active"
- **THEN** the Opportunity-Contract relationship is recorded for forecasting and reporting purposes
