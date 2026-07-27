## MODIFIED Requirements

### Requirement: View contracts associated with a contact
The system SHALL display all contracts linked to a specific Contact on the Contact detail page in a contracts section or related items panel.

#### Scenario: User views a contact with associated contracts
- **WHEN** a user navigates to a Contact detail page
- **THEN** all contracts linked to that Contact are displayed in a contracts list or table

#### Scenario: User views a contact with no contracts
- **WHEN** a user navigates to a Contact detail page with no associated contracts
- **THEN** a message indicating "No contracts" or an empty state is displayed

#### Scenario: User navigates to a contract from contact detail
- **WHEN** a user clicks on a contract link in the Contact detail page
- **THEN** the user is navigated to the contract detail page

### Requirement: Contacts can be referenced in multiple contracts
The system SHALL allow the same Contact to be associated with multiple contracts, enabling tracking of a contact's involvement across different agreements.

#### Scenario: Contact appears in multiple contracts
- **WHEN** a user views a Contact detail page
- **THEN** all contracts where that Contact is linked are displayed regardless of associated Account
