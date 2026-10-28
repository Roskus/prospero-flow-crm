# Spec Delta

## Purpose

Lets a company record what its customers pay against each invoice, by any payment method and in any currency, as income transactions of its accounts, and know at any time whether an invoice is unpaid, partially paid or paid.

## ADDED Requirements

### Requirement: Payments are income transactions
A payment of an invoice SHALL be an income transaction with status paid that references the invoice, the customer of the invoice, a payment method, an amount, a currency, the exchange rate of that currency to the company currency and the amount it settles in the invoice currency. Payments SHALL appear in the accounting transaction list like any other transaction and SHALL be included in the balance of their account.

#### Scenario: Payment listed in accounting
- **WHEN** a user registers a payment of an invoice
- **THEN** the accounting list shows an income transaction for the invoice customer referencing the invoice number

### Requirement: Payment methods and accounts
The payment methods SHALL be cash, card, transfer, MercadoPago, Bizum and other. A transfer or Bizum payment SHALL require the destination account of the company. A cash payment SHALL go to the cash account of the invoice branch when no account is chosen. A payment SHALL accept an optional reference and an optional attachment (image or PDF). The chosen account SHALL belong to the active company.

#### Scenario: Transfer without account
- **WHEN** a user registers a transfer payment without a destination account
- **THEN** the payment is not saved and a validation error is shown on the account

#### Scenario: Cash payment
- **WHEN** a user registers a cash payment of an invoice of branch "Centro" without choosing an account
- **THEN** the payment goes to the cash account of branch "Centro"

#### Scenario: Transfer with receipt
- **WHEN** a user registers a transfer payment with a reference and a PDF receipt
- **THEN** the payment stores the reference and the receipt can be downloaded from the invoice

#### Scenario: Account of another company
- **WHEN** a user registers a payment to an account of another company
- **THEN** the request returns not found and no payment is saved

### Requirement: Payment currency
A payment currency SHALL default to the invoice currency and MAY be any currency with a rate in the company. Its exchange rate SHALL default to the current company rate and be editable. When the payment currency differs from the invoice currency, the amount it settles in the invoice currency SHALL be the payment amount converted with the payment rate and the invoice rate, rounded to 2 decimals.

#### Scenario: Paying a USD invoice in ARS
- **WHEN** an invoice of 100 USD issued with rate 1200 receives a cash payment of 60000 ARS
- **THEN** the payment settles 50 USD of the invoice

### Requirement: Registering payments
Users with the `create payment` permission SHALL be able to register a payment from the invoice page, from a "Pay" action on the order list and order page shown for orders whose active invoice has an outstanding balance, and, optionally, while creating the invoice. The amount SHALL default to the outstanding balance. A payment SHALL NOT be registered on a cancelled invoice, and a payment SHALL NOT settle more than the outstanding balance of the invoice.

#### Scenario: Paying at invoicing time
- **WHEN** a user creates an invoice of 500 and fills the payment section with 500 in cash
- **THEN** the invoice is created with status paid and the cash payment is registered

#### Scenario: Payment above the balance
- **WHEN** an invoice of 500 with 300 already paid receives a payment of 300
- **THEN** the payment is not saved and a validation error says the balance is 200

#### Scenario: Pay action on an invoiced order
- **WHEN** a user with `create payment` lists orders
- **THEN** the "Pay" action is shown only on orders whose active invoice is not fully paid, and it opens the payment form of that invoice

#### Scenario: Payment on a cancelled invoice
- **WHEN** a user registers a payment on a cancelled invoice
- **THEN** the payment is not saved and an error message is shown

#### Scenario: User without payment permission
- **WHEN** a user without the `create payment` permission creates an invoice
- **THEN** the invoice form has no payment section

#### Scenario: Seller collects at invoicing time
- **WHEN** a seller creates an invoice and fills the payment section with a cash payment
- **THEN** the invoice and the payment are saved
- **AND** the seller still cannot open the accounting transaction list

### Requirement: Invoice payment status
An active invoice SHALL be issued when nothing has been settled, partially paid when the settled amount is greater than zero and lower than its total, and paid when the settled amount equals its total. The status and the outstanding balance SHALL be updated whenever a payment of the invoice is created, edited or deleted, from any screen or the API.

#### Scenario: Partial payment
- **WHEN** an invoice of 500 receives a payment of 200
- **THEN** the invoice is partially paid with a balance of 300

#### Scenario: Deleting a payment
- **WHEN** the only payment of a paid invoice is deleted from the accounting screen
- **THEN** the invoice is issued again with a balance equal to its total

#### Scenario: Editing a payment amount
- **WHEN** a payment of 500 of a paid invoice of 500 is edited to 400
- **THEN** the invoice is partially paid with a balance of 100

### Requirement: Payment fields on transactions
The transaction form and the transaction API SHALL accept and return the payment method, currency and exchange rate. Transactions without an invoice SHALL keep working as before, with currency defaulting to the currency of their account (or the company currency without account) and payment method optional.

#### Scenario: Expense without invoice
- **WHEN** a user saves an expense without invoice and without payment method
- **THEN** the transaction is saved with the currency of its account
