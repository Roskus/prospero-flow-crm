## 1. Database Migrations

- [x] 1.1 Create migration to add `last_order_number` (unsignedInteger, default 0) to the `company` table
- [x] 1.2 Create data-backfill migration that sets `company.last_order_number = COALESCE(MAX(order.order_number), 0)` for every company, based on non-deleted orders

## 2. Model Updates

- [x] 2.1 Add `last_order_number` to `Company::$fillable`
- [x] 2.2 Create `OrderObserver` with a `creating` handler: inside a transaction, lock the company row with `lockForUpdate`, call `increment('last_order_number')`, read back the new value, assign it to `$order->order_number`
- [x] 2.3 Register `OrderObserver` in `AppServiceProvider::boot()`
- [x] 2.4 Remove the order-number assignment block from `OrderRepository::save()`

## 3. Deprecate Old Counter Table

- [x] 3.1 Add a `@deprecated` PHPDoc to the `OrderNumber` model noting it is superseded by `company.last_order_number`
- [x] 3.2 Verify no other code paths write to the `order_number` table (grep for `OrderNumber::` usage and clean up or annotate)

## 4. Tests

- [ ] 4.1 Feature test: first order for a company gets `order_number = 1` (starting from `last_order_number = 0`)
- [ ] 4.2 Feature test: subsequent orders for the same company increment by 1 each time
- [ ] 4.3 Feature test: two companies' sequences are independent (same number allowed on different companies)
- [ ] 4.4 Feature test: updating an existing order does not change its `order_number` or the company's `last_order_number`
- [ ] 4.5 Feature test: company backfilled with historical max — next order continues from max + 1
- [ ] 4.6 Update `OrderRepository` tests to confirm order number is no longer assigned there
- [ ] 4.7 Run full test suite to confirm no regressions