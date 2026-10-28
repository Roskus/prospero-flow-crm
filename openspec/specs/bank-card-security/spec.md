# bank-card-security Specification

## Purpose
Limits the card data the application keeps to what is needed to identify a company card, so the security code (CVV) is never stored, shown or returned.

## Requirements

### Requirement: No card security code
The system SHALL NOT store, show or return the security code (CVV) of a card. The card form SHALL have no CVV field, the card API SHALL ignore a `cvv` input and SHALL NOT include it in responses, and the card pages SHALL NOT show it. When the change is deployed, every stored CVV SHALL be destroyed.

#### Scenario: Saving a card from the web
- **WHEN** a user saves a company card
- **THEN** the form has no CVV field and no CVV is stored

#### Scenario: Card API with a CVV
- **WHEN** an API client creates a card sending a `cvv`
- **THEN** the card is created, the CVV is not stored and the response has no `cvv`

#### Scenario: Stored CVVs after deploy
- **WHEN** the change is deployed on a database with cards that had a CVV
- **THEN** no CVV remains in the database

### Requirement: Customer card data is never stored
The system SHALL NOT store card numbers or other card data of customers. A card payment SHALL only record its payment method, amount, account and optional reference.

#### Scenario: Card payment
- **WHEN** a user registers a card payment of an invoice
- **THEN** the form asks for no card number and only the method, amount, account and reference are stored
