# Spec Delta

## Purpose

Lets a company bill its confirmed orders with invoices numbered per branch, keep them unchanged as commercial documents, lock the invoiced orders and hand the customer a PDF with the details to pay by bank transfer.

## ADDED Requirements

### Requirement: Invoice from an order
Users with permission to create invoices SHALL be able to create an invoice from an order whose status is CONFIRMED or COMPLETED and that has no active (not cancelled) invoice. The order list and the order page SHALL show a "Create invoice" action only for such orders. The invoice SHALL belong to the company and branch of the order, SHALL have the order currency and SHALL copy every order line (product, description, quantity, unit price, discount and tax) as it is at that moment. The invoice SHALL have an issue date (default today) and a due date (default the issue date, never earlier than the issue date). An order SHALL have at most one active invoice.

#### Scenario: Invoicing a confirmed order
- **WHEN** a user creates an invoice from a confirmed order with two lines
- **THEN** an invoice with the same two lines, customer, branch and currency is created

#### Scenario: Order that cannot be invoiced
- **WHEN** a user tries to invoice a PENDING or CANCELED order, or an order that already has an active invoice
- **THEN** no invoice is created and an error message is shown

#### Scenario: Create invoice action
- **WHEN** a user with create invoice permission lists orders
- **THEN** the "Create invoice" action is shown only on confirmed or completed orders without an active invoice

#### Scenario: Invoice of another company
- **WHEN** a user opens, downloads or cancels an invoice of another company by its id
- **THEN** the request returns not found

### Requirement: Order status follows its invoice
Issuing an invoice SHALL set its order to COMPLETED in the same transaction. Cancelling the invoice SHALL set the order back to CONFIRMED. A CONFIRMED order SHALL be able to go back to PENDING; a COMPLETED order SHALL NOT.

#### Scenario: Invoicing completes the order
- **WHEN** a user creates an invoice from a confirmed order
- **THEN** the order status is COMPLETED

#### Scenario: Cancelling the invoice reopens the order
- **WHEN** the invoice of a completed order is cancelled
- **THEN** the order status is CONFIRMED

#### Scenario: Completed order cannot go back to pending
- **WHEN** a user tries to set a completed order back to pending
- **THEN** the status is not changed and an error message is shown

### Requirement: Invoice totals
Each invoice line SHALL store its subtotal (quantity × unit price minus the discount percentage), its tax amount (subtotal × tax percentage) and its total, each rounded to 2 decimals. The invoice subtotal, tax and total SHALL be the sums of the rounded line values, and the invoice total SHALL include taxes.

#### Scenario: Totals of an invoice
- **WHEN** an invoice has a line of 3 × 10.00 with 10% discount and 21% tax
- **THEN** the line subtotal is 27.00, its tax is 5.67 and its total is 32.67
- **AND** the invoice total is 32.67

### Requirement: Invoice numbering per branch
Each branch SHALL number its invoices with its own sequence starting at 1, without gaps and without reusing numbers, also when invoices are created at the same time. The invoice series SHALL be the branch code at the moment of issue, and the full invoice number SHALL be the series and the number padded to 8 digits joined by a dash (`0001-00000001`). Cancelled invoices SHALL keep their number.

#### Scenario: First invoices of two branches
- **WHEN** branch `0001` and branch `0002` each issue their first invoice
- **THEN** their numbers are `0001-00000001` and `0002-00000001`

#### Scenario: Concurrent invoices of the same branch
- **WHEN** two users invoice two orders of the same branch at the same time
- **THEN** the invoices get consecutive numbers and no number is repeated

#### Scenario: Number after a cancelled invoice
- **WHEN** invoice `0001-00000005` is cancelled and the branch issues a new invoice
- **THEN** the new invoice number is `0001-00000006`

### Requirement: Invoice snapshots
An invoice SHALL store the data printed on it as it was at the moment of issue: customer name, business name, identity number and address; company business name, identity number, phone, email and website; branch code, name and address. Later changes of the customer, company or branch SHALL NOT change an issued invoice.

#### Scenario: Customer changes after invoicing
- **WHEN** the customer of an issued invoice changes its address
- **THEN** the invoice and its PDF still show the previous address

### Requirement: Invoice currency equivalent
An invoice SHALL store the exchange rate of its currency to the company currency, prefilled with the current company rate on the invoice form and editable before issuing it. When the invoice currency differs from the company currency, the invoice page and PDF SHALL show the total equivalent in the company currency together with the rate and the issue date it applies to.

#### Scenario: Invoice in a foreign currency
- **WHEN** a company whose currency is ARS issues an invoice of 100 USD with rate 1200 on 22/10/2026
- **THEN** the invoice shows "Equivalent: ARS 120,000.00 (1 USD = 1,200.00 ARS, 22/10/2026)"

#### Scenario: Invoice in the company currency
- **WHEN** the invoice currency is the company currency
- **THEN** no equivalent is shown and the exchange rate is 1

### Requirement: Invoiced orders are locked
An order with an active invoice SHALL NOT be edited, deleted, cancelled or have its lines changed, either from the web or the API. The web SHALL hide those actions and show the invoice number with a link to the invoice. The API SHALL answer such requests with a 409 conflict error. Once its invoice is cancelled, the order SHALL be editable again.

#### Scenario: Editing an invoiced order on the web
- **WHEN** a user opens the edit form or saves an order that has an active invoice
- **THEN** the order is not changed and an error message names the invoice

#### Scenario: Changing an invoiced order through the API
- **WHEN** an API client updates, deletes or changes the items of an order that has an active invoice
- **THEN** the response is 409 and the order is not changed

#### Scenario: Order editable after cancelling its invoice
- **WHEN** the invoice of an order is cancelled
- **THEN** the order can be edited and invoiced again

### Requirement: Invoice management
Users with permission to read invoices SHALL be able to list the invoices of their active company (filtered by branch when the company has several branches, by status and by customer), open an invoice and download its PDF. Users with permission to delete invoices SHALL be able to cancel an invoice that has no payments; cancelling SHALL keep the invoice and its number with status cancelled. An invoice with payments SHALL NOT be cancelled.

#### Scenario: Cancelling an invoice without payments
- **WHEN** a user cancels an invoice that has no payments
- **THEN** the invoice status is cancelled and it keeps its number

#### Scenario: Cancelling an invoice with payments
- **WHEN** a user tries to cancel an invoice that has a payment
- **THEN** the invoice is not cancelled and an error message asks to delete the payments first

#### Scenario: Overdue invoice
- **WHEN** an invoice that is not fully paid has a due date before today
- **THEN** the invoice list shows it as overdue

#### Scenario: User without invoice permission
- **WHEN** a user without read invoice permission opens the invoice list
- **THEN** the request is rejected with a forbidden error

### Requirement: Invoice PDF
The invoice PDF SHALL show the invoice number, issue and due dates, issuer (company and branch) data, customer data, lines, subtotal, tax, total with the invoice currency, the company-currency equivalent when it applies, and a "Bank transfer details" block listing the company bank accounts marked to be shown on invoices (holder, bank, IBAN or account number, SWIFT, CBU and alias when present). The block SHALL be omitted when no account is marked. The PDF SHALL state that it is not a fiscal invoice.

#### Scenario: PDF with bank accounts
- **WHEN** a company has one bank account marked to be shown on invoices and downloads an invoice PDF
- **THEN** the PDF shows the "Bank transfer details" block with that account

#### Scenario: PDF without bank accounts
- **WHEN** no bank account of the company is marked to be shown on invoices
- **THEN** the PDF has no "Bank transfer details" block

### Requirement: Invoice permissions
The system SHALL provide the permissions `create invoice`, `read invoice`, `delete invoice` (cancel) and `create payment` (register invoice payments). Company administrators and accountants SHALL have all four; sellers SHALL have `create invoice`, `read invoice` and `create payment`.

#### Scenario: Seller cancels an invoice
- **WHEN** a seller tries to cancel an invoice
- **THEN** the request is rejected with a forbidden error
