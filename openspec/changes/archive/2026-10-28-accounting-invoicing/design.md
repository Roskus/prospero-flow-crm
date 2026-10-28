# Design

## Context

See proposal.md for the motivation. These are the parts of the current code that shape the approach:

- **Currency columns**:
  - `product.currency` and `order.currency` exist with a DB default of `EUR`, but no form sets them. `OrderRepository::save()` never sets the order currency, and the web `OrderSaveRequest` only accepts it as optional.
  - `CurrencyHelper::format()` always uses the company symbol.
- **Exchange rates**:
  - Rates live in `company_currency_rate (company_id, currency, conversion_rate decimal(16,6))`.
  - The currency screen deletes and recreates every rate on save, so there is no rate history.
  - The base currency is saved with rate 1, and the column header just says "Amount".
- **Order totals**:
  - `OrderRepository::save()` computes `amount` from `$order->items` before the new items are saved. On edit this is the old relation (already deleted rows still loaded), and on create it is 0.
  - `Order::getTotal()` returns the sum of line subtotals without tax, although its comment says it includes taxes.
  - `order_item.quantity` is `tinyint unsigned`.
  - Order status changes through `OrderConfirmController` (PENDING → CONFIRMED) `OrderPendingController` (CONFIRMED → PENDING) and `OrderCancelController` (PENDING or CONFIRMED → CANCELED).
  - Issuing an invoice sets the order to COMPLETED and cancelling it sets the order back to CONFIRMED, so a completed order is an invoiced one and cannot go back to pending.
- **Numbering**: order numbers use `company.last_order_number` with `lockForUpdate()` + `increment()` in `OrderObserver`. Branches (`branch`, `is_main`, `code`, soft deletes, `BranchService` with a company lock) were added by `company-branches`.
- **Accounts and transactions**:
  - `bank_account` (model `App\Models\Bank\Account`) has types bank, stripe, paypal, mercadopago and other. Its `country_id` and `currency` are required, and `balance()` adds paid income minus paid expense to `opening_balance`.
  - `transaction` has type income/expense, status pending/paid/overdue, `bank_account_id`, `bank_card_id`, `customer_id`, `reference`, `attachment` (public disk, `accounting/{company}`) and an amount with no currency.
- **Cards**: `bank_card.cvv_encrypted` is used by:
  - `BankCard` accessors;
  - `BankCardCreateRequest` and `BankCardUpdateRequest`;
  - `BankCardSaveController`;
  - the API card controllers;
  - `bank_account/partials/_card_form`, `bank_account/show` and `bank_card/bank_card`.
- **Permissions** are defined in `PermissionSeeder` with `firstOrCreate`. Existing installs get new permissions by running that seeder.

## Goals / Non-Goals

**Goals:**
- Data that stays correct over time:
  - snapshots for invoice data and rates;
  - no number gaps or duplicates;
  - invoice payment status always derived from its payments.
- Keep invoicing logic out of controllers: services own the transactions and locks, so web, API and future features (email, QR, fiscal integration) reuse them.
- Small, separately testable migrations, with data backfills apart from DDL (as in company-branches).

**Non-Goals:**
- Rate history or automatic rate download.
- Editing an issued invoice: it is cancelled and the order invoiced again.
- Partial invoicing of an order or one invoice for several orders.
- Payments split across several invoices.
- Changing the order "Total" label, which excludes tax. It stays as is; invoices show subtotal, tax and total explicitly.

## Decisions

### Exchange rates: one service, the direction fixed by definition
- `ExchangeRateService` exposes:
  - `rate(Company, string $currency): string` returns 1 for the company currency and fails with a `ValidationException` on a missing rate;
  - `convert(string $amount, string $from, string $to, Company): string`, computed as amount × rate(from) / rate(to).
- It uses `bcmath` strings with scale 6 and rounds to 2 decimals for money.
- Alternative considered: invert the stored rate when it "looks" inverted (< 1). Rejected, because it is ambiguous for currencies close to parity. The currency screen gets the "1 XXX = ___ BASE" label instead, and the deploy notes ask admins to review their rates.

### Order currency and rate snapshot
- New column `order.exchange_rate decimal(16,6) default 1`.
- `OrderRepository::save()`:
  - sets `currency` from the request or the company currency;
  - sets `exchange_rate` from `ExchangeRateService` when the order is new or its currency changes, and otherwise keeps the stored one.
- Validation:
  - `OrderSaveRequest`, `OrderCreateRequest` and `OrderUpdateRequest` validate `currency` against the currency table;
  - the missing-rate check happens in the service and surfaces as a validation error on `currency`.
- Product prices:
  - the product autocomplete endpoint accepts an optional `currency` and returns the price converted to it, together with the original price and currency;
  - the order form sends the selected order currency.
- Line prices are not converted again when the user changes the currency of an order that already has lines. The form warns that existing line prices stay as typed, because silently converting typed prices is worse than leaving them.

### Order amount fix
`OrderRepository::save()` saves the lines first and then sets `amount` from `$order->load('items')->getTotal()`. The API item create, update and delete controllers recompute the amount the same way through a small `Order::refreshAmount()`.

### Sales figures
- `Order::getSalesSummary()`, `getSalesKpis()` and `getSalesReport()` sum `amount * exchange_rate` instead of `amount`.
- The backfill sets `exchange_rate = 1`, so existing figures do not change.

### Invoice data model
- `invoice`:
  - `id`, `company_id`, `branch_id`, `order_id` (nullable for future manual invoices), `customer_id`;
  - `series` (string 10), `number` (unsigned int), unique `(branch_id, number)`;
  - `issue_date`, `due_date`, `currency`, `exchange_rate decimal(16,6)`;
  - `subtotal`, `tax`, `total`, `paid_amount` (all `decimal(18,5)`);
  - `status` (string: issued, partially_paid, paid, cancelled), `cancelled_at`;
  - `customer_data` and `issuer_data` (JSON snapshots), `notes`, `created_by`, timestamps.
- `invoice_item`:
  - `invoice_id`, `product_id` (nullable, no FK cascade);
  - `description`, `sku`, `quantity decimal(12,3)`, `unit_price`, `discount`, `tax`;
  - `subtotal`, `tax_amount`, `total`.
- JSON snapshots instead of prefixed columns:
  - The printed data is read as a whole and never queried. Prefixing address columns would mean a second address macro and about 25 extra columns.
  - `customer_id`, `branch_id` and `company_id` stay as real columns for filtering.
  - The `Address` value object is built from the snapshot array for display.
- Status is stored, not computed, so lists filter by it in SQL. Overdue is derived at display time from `due_date` and status, because it changes with the date and not with the data.
- Models are `App\Models\Invoice` and `App\Models\Invoice\Item`, following `Order` and `Order\Item`. Constants (`ISSUED`, `PARTIALLY_PAID`, `PAID`, `CANCELLED`) follow the `Order` and `Transaction` style rather than a PHP enum, for consistency.

### Numbering per branch
- New column `branch.last_invoice_number unsigned int default 0`.
- `InvoiceService::issue()` runs in a DB transaction. It locks the branch row (`lockForUpdate`), increments `last_invoice_number`, reads it and stores `series = branch.code`. This is the same pattern as orders, scoped to the branch.
- A cancelled invoice never gives its number back, so there are no gaps or reuse.
- Locking the order row in the same transaction prevents two invoices for the same order. The service re-checks under the lock that the order has no active invoice.

### Locking invoiced orders
- `Order::activeInvoice()` is a HasOne relation where the status is not cancelled, and `Order::isInvoiced(): bool` builds on it.
- Checks:
  - the web update and save controllers, the delete and confirm controllers, and the API update, delete and item controllers check it;
  - the web shows a flash error, and the API returns `409` with a message;
  - the check sits in each controller, right after the order is loaded, instead of a global observer, so each entry point answers with its own format.
- `OrderObserver::updating` throws as a safety net, but must allow `amount` refreshes. It is skipped when only `updated_at` changes.

### Payments as transactions
- New `transaction` columns:
  - `invoice_id` (nullable FK, `restrictOnDelete`);
  - `currency` (string 3, nullable for old rows, backfilled from the account or company);
  - `exchange_rate decimal(16,6) default 1`;
  - `invoice_amount decimal(18,5) nullable`;
  - `payment_method` (string 20, nullable).
- `Transaction::PAYMENT_METHODS` holds the constants cash, card, transfer, mercadopago, bizum and other.
- `InvoiceService::registerPayment(Invoice, array)` validates the balance and account rules, defaults the cash account, computes `invoice_amount` and creates the income transaction, all under a lock of the invoice row.
- Registering a payment is gated by its own `create payment` permission, not `create transaction`. Sellers invoice and collect, but the accounting screen (expenses, editing or deleting any transaction) stays with `create/read/update/delete transaction`. `registerPayment()` is the only way that permission creates a transaction.
- A `TransactionObserver` (saved, deleted, restored) calls `InvoiceService::refreshPaymentStatus()` when `invoice_id` is set or changed (old and new invoice). It recalculates `paid_amount = SUM(invoice_amount)` of the paid transactions and the status.
  - This keeps status correct for edits and deletes made from the accounting screen or the API.
  - Alternative considered: computing status on read. Rejected, because lists need to filter and sort by status.
- The transaction form shows the payment fields (method, currency, rate). It shows the invoice as a read-only link when present. It does not let users attach existing transactions to invoices in this change.

### Cash accounts
- Account changes:
  - `Account::TYPE_CASH = 'cash'`;
  - new columns `bank_account.branch_id` (nullable FK) and `show_on_invoice boolean default false`;
  - `country_id` stays required, and the cash account takes the company country (falling back to the branch country, then `es`).
- `CashAccountService::createForBranch(Branch)` is idempotent: it returns the existing cash account of the branch. It is called from a `BranchObserver::created`, and the backfill migration covers existing branches.
- The name is built as `__('Cash').' '.$branch->name` at creation time. It is stored data the user can rename.
- `BankAccountDeleteController` refuses to delete a cash account whose branch is not deleted.

### CVV removal
- One migration drops `bank_card.cvv_encrypted`, and its `down()` recreates the nullable column, without data.
- The model, requests, controllers (web and API), views and OpenAPI attributes stop using CVV. The API request rules drop `cvv`; unknown input is ignored, as for any unvalidated field.

### Branch code lock
`BranchSaveRequest` adds a rule: when the branch has invoices, `code` must equal the stored code. The branch form renders the code `readonly` with the hint "It is the series of its invoices".

### Screens and routes
- **Routes**: a new `routes/module/invoice.php` file (required from `web.php`), with:
  - `GET /invoice`;
  - `GET /invoice/create/{order_number}` and `POST /invoice/save`;
  - `GET /invoice/show/{id}` and `GET /invoice/download/{id}`;
  - `POST /invoice/cancel/{id}`;
  - `POST /invoice/payment/{id}`.
- **Controllers**: single-action-style classes under `App\Http\Controllers\Invoice`, like the other modules.
- **Menu**: an "Invoices" item next to Orders in the top menu and in `menu.php`, gated by `read invoice`.
- **PDF**: `resources/views/invoice/pdf.blade.php` rendered with dompdf, like `order.pdf`. Formatting uses a new `CurrencyHelper::formatIn(amount, currency)` that takes the symbol of the given currency.

## Risks / Trade-offs

- [Existing rates may have been entered in the opposite direction] → The deploy note asks admins to review the currency screen. The new "1 USD = ___ ARS" label makes a wrong value obvious, and existing orders get `exchange_rate = 1`, so no historic figure changes.
- [Backfilling `currency` overwrites an explicit `EUR` sent through the API by a non-EUR company] → The backfill only touches rows still at the DB default `EUR` in companies whose currency is not EUR. Before this change the web never set the currency, so the API case is rare. This is documented in the migration plan.
- [Dropping the CVV is irreversible] → It is the intended outcome (PCI). The migration `down()` recreates the column empty.
- [JSON snapshots cannot be queried] → Filters use the real `customer_id` and `branch_id` columns. Searching by printed customer name uses the `customer` join.
- [Payments in a third currency depend on two rates typed by users] → Both rates are stored on the records (invoice and transaction), so the settled amount can always be explained and recomputed.
- [Number lock contention on busy branches] → The lock covers only the branch row, inside a short transaction, as orders already do.
- [Order quantity is `tinyint`] → Invoice quantity is `decimal(12,3)` so it does not inherit the limit. Widening `order_item.quantity` is left for a later change.

## Migration Plan

1. Run the migrations in order:
   - DDL for orders, then backfill: `order.exchange_rate`, then the currency backfill of products and orders;
   - transaction payment columns, then the backfill of `transaction.currency`;
   - `bank_account` cash columns, then the cash account per branch backfill;
   - `branch.last_invoice_number`;
   - the `invoice` and `invoice_item` tables;
   - the drop of `bank_card.cvv_encrypted`.
2. Run `php artisan db:seed --class=PermissionSeeder` to create the invoice permissions.
3. Admins review the currency rates screen.

Rollback: every migration has a `down()`. The CVV data cannot be restored by design. Invoices created after deploy are lost on rollback of the invoice tables.
