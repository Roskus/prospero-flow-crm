# Tasks

## 1. Exchange rates

- [x] 1.1 Create `ExchangeRateService` (`rate()` with 1 for the company currency and a `ValidationException` on a missing rate, `convert()` with bcmath, 2-decimal money rounding) and `CurrencyHelper::formatIn(amount, currency)`; verify with unit-style feature tests for company currency, foreign to company, foreign to foreign (120 USD → 110.77 EUR) and missing rate
- [x] 1.2 Relabel the currency rates screen to "1 XXX = ___ BASE" per row (base row read only) and add translations; verify with a feature test that the USD row of an ARS company shows "1 USD" and "ARS"

## 2. Product and order currency

- [x] 2.1 Add the currency select (existing currency component) to the product form, default the company currency for new products, validate it in the product requests (web and API) and show prices with their currency in the product list; verify with feature tests for the default, saving USD and the list
- [x] 2.2 Create the data migration that sets `product.currency` and `order.currency` to the company currency for rows still at `EUR` in non-EUR companies; verify with a migration test that EUR companies are untouched
- [x] 2.3 Add `order.exchange_rate decimal(16,6) default 1` and make `OrderRepository` set `currency` (request or company) and snapshot `exchange_rate` on create or currency change; validate `currency` against the currency table in the web and API order requests and return `exchange_rate` in the API and OpenAPI schema; verify with web and API tests for default currency, rate kept after a rate change, rate refreshed on currency change and missing rate error
- [x] 2.4 Fix the order amount: save lines first, then set `amount` from the reloaded items; add `Order::refreshAmount()` and call it from the API item create/update/delete controllers; verify with tests for create, edit and API item changes
- [x] 2.5 Make the product autocomplete accept `currency` and return the converted price, and add the order currency select to the order form (sending it to the autocomplete, with a note that existing line prices are not converted); verify with a feature test of the autocomplete conversion and the form rendering
- [x] 2.6 Convert sales figures with `amount * exchange_rate` in `getSalesSummary()`, `getSalesKpis()` and `getSalesReport()`; verify with a test of an ARS company with an ARS and a USD order totalling 220000

## 3. Cash accounts and bank account options

- [x] 3.1 Add `Account::TYPE_CASH`, `bank_account.branch_id` (nullable FK) and `show_on_invoice` (default false); make the account form hide bank fields for cash and offer "Show on invoices" for non-cash accounts; adjust account requests; verify with feature tests for saving a cash account with only name and currency and toggling the flag
- [x] 3.2 Create `CashAccountService::createForBranch()` (idempotent, company currency, company or branch country, name `__('Cash').' '.$branch->name`), call it from a `BranchObserver::created`, and create the backfill migration for existing branches; verify with tests for a new branch, the main branch of a new company and the migration run twice
- [x] 3.3 Refuse deleting the cash account of a non-deleted branch in `BankAccountDeleteController` (and the API if it deletes accounts); verify with a feature test

## 4. CVV removal

- [x] 4.1 Drop `bank_card.cvv_encrypted` (down recreates it empty) and remove CVV from `BankCard`, its requests, web and API controllers, views and OpenAPI attributes; verify with web and API tests that a sent `cvv` is ignored and never returned, and `grep -ri cvv app resources` returns nothing

## 5. Invoices

- [x] 5.1 Create the `invoice` and `invoice_item` migrations, `branch.last_invoice_number`, the `Invoice` and `Invoice\Item` models (constants, casts, relations, `fullNumber()`, `balance()`, `isOverdue()`, inline OAT schema) with factories, and `Order::activeInvoice()` / `isInvoiced()`; verify with `php artisan migrate` / rollback and a model test
- [x] 5.2 Create `InvoiceService::issue(Order, array)`: transaction, lock order and branch, re-check status and active invoice, number from `last_invoice_number`, series from the branch code, copy lines with rounded subtotal/tax/total, snapshots of customer, company and branch, exchange rate; verify with tests for lines and totals (3 × 10.00, 10% discount, 21% tax → 32.67), numbering per branch, number after a cancelled invoice, order set to COMPLETED, refused PENDING/CANCELED/already invoiced orders and snapshot unchanged after a customer edit
- [x] 5.3 Add the invoice permissions to `PermissionSeeder` (`create invoice`, `read invoice`, `delete invoice`, `create payment`: admin and accountant all, seller all but `delete invoice`), `routes/module/invoice.php`, the "Create invoice" action on the order list and page, the invoice form (dates, rate prefilled, notes) and `InvoiceSaveRequest`; verify with feature tests for the action visibility, creating an invoice, 404 for another company and 403 for a seller cancelling
- [x] 5.4 Create the invoice list (branch filter when several branches, status and customer filters, overdue badge), invoice page with equivalent in company currency, cancel action (refused with payments, order set back to CONFIRMED) and the "Invoices" menu entries; verify with feature tests for filters, equivalent text, overdue, cancel and cancel refused
- [x] 5.5 Create the invoice PDF (dompdf) with issuer, branch, customer snapshots, lines, totals, equivalent, "Bank transfer details" from accounts with `show_on_invoice`; verify with view render tests with and without marked accounts and a download test
- [x] 5.6 Lock invoiced orders: web edit/save/delete/confirm show a flash error naming the invoice and hide the actions, API update/delete/item endpoints return 409, `OrderObserver::updating` guard allows amount refreshes; verify with web and API tests and a test that the order is editable after cancelling its invoice
- [x] 5.7 Make the branch code read only when the branch has invoices (`BranchSaveRequest` rule and readonly field with hint); verify with feature tests
- [x] 5.8 Add every invoice translation to `lang/en.json` and `lang/es.json`; verify with a key coverage test over the invoice views, controllers and requests and a Spanish rendering test

## 6. Payments

- [ ] 6.1 Add `transaction.invoice_id`, `currency`, `exchange_rate`, `invoice_amount`, `payment_method` and the data migration backfilling `currency` from the account or company; add `Transaction::PAYMENT_METHODS`, `invoice()` relation and the fields to the transaction form, requests, API and OpenAPI; verify with tests for an expense without invoice keeping its account currency and the API returning the new fields
- [ ] 6.2 Create `InvoiceService::registerPayment()` (invoice lock, cancelled invoice refused, account of the company, transfer/Bizum require account, cash defaults to the branch cash account, currency and rate defaults, `invoice_amount` conversion, balance check) and `refreshPaymentStatus()`; verify with tests for each rule, 60000 ARS settling 50 USD and the balance error
- [ ] 6.3 Create `TransactionObserver` calling `refreshPaymentStatus()` on saved, deleted and restored for the old and new invoice; verify with tests for partial payment, deleting the only payment and editing an amount from the accounting screen and the API
- [ ] 6.4 Add the payment form to the invoice page and the optional payment section to the invoice form (only with `create payment`), with attachment and reference, the "Pay" action on the order list and page for orders whose active invoice is not fully paid, and list payments with their receipts on the invoice; verify with feature tests for paying at invoicing time, registering a transfer with a receipt a seller collecting at invoicing time and the section hidden without `create payment`

## 7. Integration checks

- [ ] 7.1 Run `php artisan migrate:fresh --seed` on the test database and `php artisan migrate` on a copy of existing data; verify every branch has one cash account, products and orders have the company currency and no CVV column remains
- [ ] 7.2 Run the full suite, `vendor/bin/pint --dirty --format agent`, Larastan on the changed files and `openspec validate accounting-invoicing --strict`; verify everything passes
