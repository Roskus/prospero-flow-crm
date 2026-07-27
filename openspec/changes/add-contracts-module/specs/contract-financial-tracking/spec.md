## ADDED Requirements

### Requirement: Track contract value and currency
The system SHALL allow users to specify a Contract Value (monetary amount) and Currency for each contract. Currency defaults to the company or user's default currency.

#### Scenario: User sets contract value and currency during creation
- **WHEN** a user creates a contract and enters a Contract Value and selects a Currency
- **THEN** the values are saved and displayed on the contract detail page

#### Scenario: User updates contract value
- **WHEN** a user edits a contract and changes the Contract Value
- **THEN** the updated value is persisted and displayed

### Requirement: Manage contract line items
The system SHALL allow users to add, edit, and delete line items associated with a contract. Each line item represents a distinct good or service within the contract.

#### Scenario: User adds a line item to a contract
- **WHEN** a user clicks "Add Line Item" and provides a description and amount
- **THEN** the line item is created and associated with the contract

#### Scenario: User edits a line item
- **WHEN** a user updates the description or amount of an existing line item
- **THEN** the line item is updated in the database

#### Scenario: User deletes a line item
- **WHEN** a user deletes a line item from a contract
- **THEN** the line item is removed and totals are recalculated

#### Scenario: Line items display on contract detail page
- **WHEN** a user views a contract detail page
- **THEN** all line items are displayed in a table with description, quantity, unit price, and line total

### Requirement: Calculate and track subtotal
The system SHALL calculate the Subtotal as the sum of all line item totals (quantity × unit price) and display it on contracts and line item tables.

#### Scenario: Subtotal updates when line item is added
- **WHEN** a user adds a line item with an amount of 100
- **THEN** the Subtotal increases by 100 and is displayed

#### Scenario: Subtotal updates when line item is removed
- **WHEN** a user deletes a line item that was worth 100
- **THEN** the Subtotal decreases by 100

### Requirement: Apply and track discounts
The system SHALL allow users to specify a Discount amount (monetary value or percentage) that is applied to the Subtotal to calculate the discounted subtotal.

#### Scenario: User applies a fixed discount
- **WHEN** a user enters a fixed discount amount of 50 on a contract with Subtotal 1000
- **THEN** the system displays Subtotal 1000, Discount 50, and Discounted Subtotal 950

#### Scenario: User applies a percentage discount
- **WHEN** a user enters a percentage discount of 10% on a contract with Subtotal 1000
- **THEN** the system calculates Discount 100 and Discounted Subtotal 900

#### Scenario: User updates discount
- **WHEN** a user changes a discount value
- **THEN** all totals are recalculated automatically

### Requirement: Track taxes
The system SHALL allow users to specify a Tax amount (percentage or fixed) that is applied to the discounted subtotal (after discount).

#### Scenario: User applies a tax percentage
- **WHEN** a user enters a tax percentage of 10% on a contract with Discounted Subtotal 950
- **THEN** the system calculates Tax 95 and adds it to the total

#### Scenario: User applies a fixed tax amount
- **WHEN** a user enters a fixed tax of 50
- **THEN** the tax amount is applied to the Discounted Subtotal

#### Scenario: Tax is recalculated when discount changes
- **WHEN** a user modifies a discount and tax is applied as a percentage
- **THEN** tax is recalculated based on the new discounted subtotal

### Requirement: Track shipping and shipping tax
The system SHALL allow users to specify a Shipping amount and Shipping Tax (which applies only to the Shipping amount, not the subtotal).

#### Scenario: User adds shipping cost
- **WHEN** a user enters a Shipping amount of 25
- **THEN** the Shipping is displayed and included in final totals

#### Scenario: User applies shipping tax
- **WHEN** a user enters Shipping Tax of 10% and Shipping is 25
- **THEN** the system calculates Shipping Tax as 2.50 and adds it to the total

#### Scenario: Shipping and shipping tax are independent of discount and tax
- **WHEN** a user modifies discount and tax
- **THEN** Shipping and Shipping Tax remain unchanged

### Requirement: Calculate grand total
The system SHALL calculate Grand Total as: Subtotal - Discount + Tax + Shipping + Shipping Tax, and display it prominently on contracts.

#### Scenario: Grand total calculation with all components
- **WHEN** a contract has Subtotal 1000, Discount 50, Tax 95, Shipping 25, Shipping Tax 2.50
- **THEN** the system calculates and displays Grand Total 1072.50

#### Scenario: Grand total updates in real-time
- **WHEN** a user modifies any financial field
- **THEN** Grand Total is recalculated immediately (in form with JavaScript) and persisted when saved

#### Scenario: Grand total display on contract list
- **WHEN** a user views the contract listing
- **THEN** each contract row shows its Grand Total for quick financial visibility

### Requirement: Financial validation
The system SHALL validate that all financial fields (amounts) are non-negative numbers and that calculations are accurate before saving.

#### Scenario: User enters invalid financial amount
- **WHEN** a user enters a negative or non-numeric value in a financial field
- **THEN** the system displays a validation error and prevents saving

#### Scenario: System persists accurate financial data
- **WHEN** a contract with calculated totals is saved
- **THEN** all calculations are verified on the backend and persisted accurately
