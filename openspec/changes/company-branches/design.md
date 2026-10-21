# Design

## Context

- Company data (address, phone, email) lives on `company`. Companies are created from the company form (`CompanyRepository`), by a MultiCompanyAdmin within quota and by the `company:create` command; `CompanyObserver` already reacts to company updates, deletes and restores.
- Orders are created from the web (`OrderRepository`) and the API (`Api\Order\OrderCreateController`). `OrderObserver::creating()` already assigns the per-company `order_number`.
- Every query filters by `Auth::user()->company_id` (the active company). Users of multi-company access have a `default_company_id`.
- Company permissions: `read company` and `update company` (CompanyAdmin and SuperAdmin), `create company` / `delete company`.
- Settings screen groups entries in Organization, Finance, Catalog and Communication.

## Goals / Non-Goals

**Goals:**
- Branches as a lightweight child of the company, with no change to data isolation (still by company).
- Every order always has a branch, whatever path creates it.
- Leave the data ready for the `accounting-invoicing` change (invoice series and cash account per branch).

**Non-Goals:**
- Restricting what a user can see by branch (permissions per branch). Users still see all data of the active company.
- Stock per branch, branch on leads, customers, tickets or transactions.
- Reports per branch beyond the order list filter.
- Invoice numbering, cash accounts and any accounting behavior (next change).

## Decisions

### Address reused from `reusable-address`
The `branch` migration uses `$table->address()`, the model uses `HasAddress`, the request merges `AddressRules::rules()` and the form and list use the address components. The main branch copies the company address through `$branch->address = $company->address`.

### `is_main` flag instead of `company.main_branch_id`
`branch.is_main` boolean. A `company.main_branch_id` FK would guarantee "exactly one" at database level, but creates a circular reference (company needs a branch, branch needs a company) and forces a nullable column during company creation. With the flag, the "exactly one" rule is kept by a `BranchService::makeMain()` that, inside a transaction locking the company row, clears the previous main branch and sets the new one; deleting the main branch is refused.

### Main branch created in `CompanyObserver::created()`
One place covers the three creation paths (form, MultiCompanyAdmin, command) and any future one. The observer copies the company `address`, `phone` and `email` and uses code `0001` and the company name. Existing companies get the same data through a data migration using the query builder (models and observers may change later; a migration must stay reproducible), including soft deleted companies so restoring them keeps the invariant.

### Branch code: string, unique per company including deleted branches
`code` `varchar(10)` with a unique index on (`company_id`, `code`). The index also covers soft deleted branches on purpose: the code will be the invoice series prefix, and reusing the code of a deleted branch would mix two series. The form suggests `max(numeric codes) + 1` padded to 4 digits; users may type any code (letters allowed) for companies with their own convention.

### Current branch resolved on demand
`User::currentBranch(): Branch` returns `default_branch_id` when it is a non deleted branch of the active company, otherwise the active company's main branch. It is memoized for the request and only called where a branch is needed (order form/save, branch filter), so the rest of the requests do not run extra queries. `user.default_branch_id` is not cleared when a branch is deleted or when a multi-company user switches company: the resolution rule already falls back to the main branch, and the user gets their branch back when switching to their default company.

### `user.default_branch_id` always stored
Named like `default_company_id` because it belongs to the user's default company, not to the active one. Unlike `default_company_id` it is stored for every user (set to the main branch on creation and backfilled), so the user form shows the real value and the fallback to the main branch is only needed for deleted branches and client companies. The user creation paths (user form, employee creation in RRHH, `company:create` command) set it through one `User` `creating` hook when it is empty. The column stays nullable at database level so a user can be created before their company's branches in seeders, and the hook fills it.

### Order branch set in `OrderObserver::creating()`
`order.branch_id` is assigned from the request when present (validated with `Rule::exists('branch','id')->where('company_id', ...)->whereNull('deleted_at')`) and otherwise defaulted in `OrderObserver::creating()` to the creator's current branch, falling back to the company's main branch when there is no authenticated user (seeders, commands). The column is added nullable, backfilled with each company's main branch, then made `NOT NULL` in the same migration.

### Permissions reuse company permissions
Branches are part of the company configuration, so listing uses `read company` and create/update/delete/set main use `update company`. No new permissions or seeder changes. Reading branches for selects (order, user form) needs no permission beyond the screen being shown.

### Order list filter and column only with several branches
`Company::hasSeveralBranches()` (cached per request) hides the branch select, column and filter for single branch companies, which will be most of them, so their screens do not change.

## Risks / Trade-offs

- [Two main branches after a race between two "set as main" requests] → `makeMain()` locks the company row and runs in a transaction; a test covers that the previous main branch is cleared.
- [Backfilling `order.branch_id` on large databases] → one `UPDATE order JOIN branch ON branch.company_id = order.company_id AND branch.is_main = 1` statement instead of a per-row loop.
- [Company address and main branch drift apart after editing the company] → accepted; they are different concepts (legal address vs location). The company form shows a hint linking to the branches screen.
- [Orders of soft deleted branches] → orders keep the reference and load the branch `withTrashed()` for display.

## Migration Plan

1. Migration creating `branch` (FK `company_id` cascade on delete, soft deletes, unique `company_id` + `code`).
2. Data migration creating the main branch of every company (including soft deleted).
3. Migration adding nullable `user.default_branch_id` (FK, null on delete) and `order.branch_id` (FK), backfilling users and orders with their company's main branch and making `order.branch_id` not null.

Rollback: the `down()` methods drop the columns and the table; no other data is changed.
