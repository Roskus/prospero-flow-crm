## Why

The `order_number` field on orders exists but the sequencing logic has gaps: there is no guarantee the `OrderNumber` counter record is initialized before the first order for a company, and the logic lives inside `OrderRepository` — meaning any other order-creation path bypasses it. This change simplifies the approach by moving the counter directly onto the `company` record as a single `last_order_number` field, eliminating the separate `order_number` table.

## What Changes

- Add `last_order_number` (unsignedInteger, default 0) to the `company` table — this is the single source of truth for each company's current order sequence.
- **Data backfill**: for companies that already have orders, `last_order_number` is seeded from `MAX(order.order_number)` so existing sequences are not disrupted.
- Move order number assignment out of `OrderRepository` into an Eloquent model observer (`OrderObserver`) on the `creating` event — centralized and impossible to bypass.
- In the observer, atomically increment `company.last_order_number` with a locked update and assign the result to `order.order_number`.
- Deprecate (and eventually remove) the `order_number` table — it is no longer needed.
- **No breaking change** to the `order_number` column on `order` — the value just comes from a different source.

## Capabilities

### New Capabilities

- `company-order-sequence`: Per-company order number sequencing — auto-increment stored directly on the company as `last_order_number`, concurrency-safe, seeded for existing companies from their historical max.

### Modified Capabilities

- (none)

## Impact

- **Migration A**: add `last_order_number` (unsignedInteger, default 0) to `company` table.
- **Migration B**: backfill `company.last_order_number = MAX(order.order_number)` for each company that already has orders.
- **New**: `OrderObserver` (creating event) — increments `company.last_order_number` atomically and sets `order.order_number`.
- **Removed logic from**: `OrderRepository::save()`.
- **Deprecated**: `order_number` table and `OrderNumber` model (can be removed in a follow-up).
- **Tests**: observer tests + updated repository tests.