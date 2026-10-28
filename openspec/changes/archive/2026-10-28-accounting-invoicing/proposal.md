# Proposal

## Why

Orders are the last commercial step today: there is no way to bill a confirmed order, record that the customer paid it or know how much is still owed. Companies also sell in more than one currency (for example ARS and USD) but `product.currency` and `order.currency` are never set from the UI, so every amount is silently treated as EUR and the rates in `company_currency_rate` have no defined direction. This change adds the first part of accounting: currencies that work end to end, invoices issued from orders, payments recorded against invoices and a cash account to receive them. Fiscal integration (AFIP/ARCA, Verifactu, DGI) is a later change; invoices here are commercial documents.

## What Changes

- **Exchange rates**: `company_currency_rate.conversion_rate` is defined as "units of the company currency per 1 unit of the foreign currency" and the currency screen says so.
- **Product currency**: products get a currency select defaulting to the company currency. Existing products are backfilled to their company currency.
- **Order currency**: one currency per order, chosen in the order form (web and API) and defaulting to the company currency. The rate to the company currency is snapshotted in a new `order.exchange_rate` column. Product prices in another currency are converted when added to the order. Existing orders are backfilled to their company currency.
- **Sales figures**: dashboard and sales report figures convert each order to the company currency with its snapshotted rate instead of adding amounts of different currencies.
- **Order amount fix**: `order.amount` is computed after the items are saved (today it is computed from the previous items).
- **Invoices**: new `invoice` and `invoice_item` entities. A "Create invoice" action on CONFIRMED or COMPLETED orders without an active invoice copies the order lines and snapshots the customer, issuer and branch data.
  - Each branch numbers its invoices with its own sequence, using the branch code as series (`0001-00000001`).
  - The invoice has its own exchange rate (prefilled with the current company rate) and shows the equivalent amount in the company currency.
  - Invoices can be listed, viewed, downloaded as PDF and cancelled when they have no payments. A cancelled invoice keeps its number.
  - The PDF includes a "Bank transfer details" block with the company accounts marked to be shown on invoices.
- **Invoiced orders are locked**: an order with an active invoice cannot be edited, deleted or have its items changed (web and API) until the invoice is cancelled.
- **Payments are transactions**: `transaction` gets `invoice_id`, `currency`, `exchange_rate`, `invoice_amount` and `payment_method`.
  - Payment methods are cash, card, transfer, MercadoPago, Bizum and other.
  - A transfer or Bizum payment requires the destination account, and a cash payment goes to the branch cash account. Reference and attachment are optional.
  - Payments can be registered from the invoice, or at invoicing time. The invoice status (issued, partially paid, paid) follows the payments, also when a payment is edited or deleted.
- **Cash accounts**: new `cash` bank account type. Every branch gets a cash account automatically (existing branches are backfilled), and it cannot be deleted while the branch exists.
- **Bank accounts on invoices**: bank accounts get a `show_on_invoice` flag.
- **BREAKING: CVV removal**: the card CVV is no longer stored (PCI). The `bank_card.cvv_encrypted` column and the CVV fields of the web form and API are removed, and existing CVVs are destroyed.
- **Branch code locked**: the code of a branch cannot be changed once the branch has issued invoices, because it is the invoice series.

Out of scope (later changes): reusable card component and accounting reports (part 2), invoice QR with public view, sending invoices by email, online payment, fiscal integration, invoices API, credit notes.

## Capabilities

### New Capabilities

- `multi-currency`: currency of products and orders, exchange rate direction and snapshots, conversion of prices and sales figures to the company currency.
- `invoicing`: invoices issued from orders, per-branch numbering, snapshots, locking of invoiced orders, cancellation, PDF with bank transfer details.
- `invoice-payments`: payments as transactions linked to invoices, payment methods and their account rules, invoice payment status.
- `cash-accounts`: cash account type and one cash account per branch.
- `bank-card-security`: card data the application stores, with no CVV.

### Modified Capabilities

- `company-branches`: the branch code cannot change once the branch has invoices.

## Impact

- **Database**:
  - new tables `invoice` and `invoice_item`;
  - new columns `order.exchange_rate`, `branch.last_invoice_number`, `bank_account.branch_id`, `bank_account.show_on_invoice`, and the payment columns on `transaction`;
  - backfills of product and order currency and of the cash accounts;
  - drops `bank_card.cvv_encrypted`.
- **Code**:
  - new models `Invoice` and `Invoice\Item`, plus `InvoiceService`, `ExchangeRateService` and `CashAccountService`;
  - invoice controllers, requests, views and PDF;
  - a transaction observer;
  - changes to the product, order, transaction, bank account and bank card forms and requests, the API order, transaction and bank card controllers, the order repository and the sales figures in `Order`.
- **Permissions**: new `create invoice`, `read invoice`, `delete invoice` (cancel) and `create payment` (register invoice payments) in `PermissionSeeder`. Sellers get all but `delete invoice`, so they can invoice and collect without access to the rest of accounting. Deploys run `php artisan db:seed --class=PermissionSeeder`.
- **API**:
  - order endpoints accept and return `currency` and `exchange_rate`, and reject changes to invoiced orders with 409;
  - bank card endpoints no longer accept or return `cvv`;
  - transaction endpoints accept and return the payment fields.
- **Translations**: new keys in `lang/en.json` and `lang/es.json`.
- **Dependencies**: none. The PDF uses the existing dompdf.
