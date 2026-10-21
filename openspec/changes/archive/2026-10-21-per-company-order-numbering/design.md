## Context

The `order_number` column on `order` and a separate `order_number` counter table exist since April 2023. The counter table approach has two structural problems:

1. **Initialization bug**: if no `OrderNumber` row exists for a company, `update()` silently affects 0 rows — the counter is never persisted, and subsequent orders can collide.
2. **Scattered logic**: the assignment lives in `OrderRepository::save()`, so any other order-creation path (API controllers, factories, seeders) bypasses it.

The simplest fix is to collapse the separate `order_number` table into a single `last_order_number` column on `company` — one field, one place, always initialized (it has a default).

## Goals / Non-Goals

**Goals:**
- Each company tracks its own order sequence in a single `last_order_number` field on the `company` table.
- Existing companies are backfilled so their sequence continues from the highest existing `order_number`.
- Order number assignment is concurrency-safe (no duplicate numbers under parallel inserts).
- Logic is centralized in an Eloquent observer so no creation path can bypass it.

**Non-Goals:**
- Changing the display format of order numbers (zero-padding in `Order::orderNumber()` is unchanged).
- Database-level triggers — PHP observers are testable and sufficient.
- Retroactively renumbering historical orders.
- Providing a UI to manually reset or override `last_order_number` (admin concern, out of scope).

## Decisions

### 1. `last_order_number` on `company`, not a separate table

**Decision**: Add `last_order_number unsignedInteger default 0` directly to the `company` table instead of the existing `order_number` lookup table.

**Why**: Eliminates the initialization bug entirely (the column always exists with a default), removes a join/lookup on every order creation, and makes the state visible wherever the company record is already loaded. One field, one source of truth.

**Alternative considered**: Fix the existing `OrderNumber` table by adding `firstOrCreate` — still requires a separate table, a separate model, and a lookup on every creation. Extra complexity with no benefit.

### 2. Observer on `creating` event

**Decision**: Move all order number assignment into `OrderObserver::creating()`.

**Why**: Fires on every `Order` model creation regardless of call site. Keeps `OrderRepository` free of sequence concerns. Fully unit-testable in isolation.

**Alternative considered**: Leave logic in `OrderRepository` and audit all other creation paths — fragile and requires ongoing discipline.

### 3. Atomic increment via locked `UPDATE … RETURNING` pattern

**Decision**: Inside a transaction, run:
```sql
UPDATE company SET last_order_number = last_order_number + 1 WHERE id = ?
```
then reload `company->last_order_number` to get the assigned value.

In Eloquent: `Company::where('id', $companyId)->lockForUpdate()->increment('last_order_number')` followed by a fresh `value('last_order_number')` read — all within the wrapping transaction.

**Why**: Atomic at the DB level; no race between read and write. Works with MySQL without needing triggers.

### 4. Deprecate `order_number` table

**Decision**: Mark the `order_number` table and `OrderNumber` model as deprecated in this change; schedule removal in a follow-up once the observer is proven stable.

**Why**: Avoids a big-bang removal during the same deploy. The table becomes a harmless dead artifact while we validate the new approach.

## Risks / Trade-offs

| Risk | Mitigation |
|---|---|
| Existing companies have orders but no corresponding counter — next order would get number 1 and collide | Migration B backfills `last_order_number = MAX(order.order_number)` per company before observer goes live |
| A company may have had orders with `order_number = NULL` (e.g. created before the column existed) | Backfill uses `COALESCE(MAX(order_number), 0)` — nulls are ignored and the counter starts at 0 safely |
| `company.last_order_number` could drift if bulk inserts bypass Eloquent | Document: bulk `Order::insert()` is unsupported; callers must use `Order::create()` or the repository |
| Changing `last_order_number` directly in the DB (e.g. support reset) breaks the sequence | Out of scope; treat as an admin responsibility with explicit documentation |

## Migration Plan

1. **Migration A** — `ALTER TABLE company ADD last_order_number UNSIGNED INT NOT NULL DEFAULT 0`. Zero-downtime; column has a default.
2. **Migration B** — data backfill:
   ```sql
   UPDATE company c
   SET c.last_order_number = (
       SELECT COALESCE(MAX(o.order_number), 0)
       FROM `order` o
       WHERE o.company_id = c.id AND o.deleted_at IS NULL
   );
   ```
3. Register `OrderObserver` in `AppServiceProvider::boot()`.
4. Remove order-number assignment from `OrderRepository::save()`.
5. Run full test suite; deploy.

**Rollback**: drop `last_order_number` from `company`; revert `OrderRepository`; unregister observer. The `order` table `order_number` column is unaffected.

## Open Questions

- Should `last_order_number` be exposed (read-only) in the Company API resource so clients can display "next order will be #N+1"?