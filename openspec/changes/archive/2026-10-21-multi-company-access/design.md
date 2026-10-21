# Design

## Context

- Every user belongs to one company through `user.company_id`. About 200 places filter data with `Auth::user()->company_id` (controllers, form requests, repositories, views). There is no global company scope.
- Roles and permissions use Spatie Permission with global roles (no teams). `create company` and `delete company` belong to SuperAdmin only; `read company` and `update company` also to CompanyAdmin.
- The company list (`CompanyIndexController`) is only authorized for SuperAdmin (`CompanyIndexRequest`); other roles get 403.
- `CompanySaveRequest` lets a non SuperAdmin update only `Auth::user()->company_id`.
- Security events go through `App\Services\SecurityLogger` (`security` log channel).
- The web uses session auth and the API uses JWT; both resolve the user from the database on every request.

## Goals / Non-Goals

**Goals:**
- Let a user work in several companies without touching the ~200 existing company filters.
- Keep data isolation exactly as it is today: at any moment a user only reads and writes data of one company.
- Keep the quota a plain number so a private billing module can change it later.

**Non-Goals:**
- Per-company roles (Spatie teams). The user keeps the same global roles in every company.
- Impersonation of other users.
- Billing, payments or license keys.
- Showing data from several companies at once (consolidated reports).
- A different active company per browser tab.

## Decisions

### 1. Active company is `user.company_id`, switched in place
Switching updates `user.company_id` after checking access. Every existing filter keeps working unchanged, for web and API.

- *Alternative: active company in the session plus an accessor that overrides `company_id`.* Rejected: it only works for session auth (not JWT), and an accessor that changes an attribute depending on the request is fragile and confusing in queues, commands and tests.
- *Alternative: global scope per model.* Rejected for this change: it means refactoring ~200 call sites and every model, with a high risk of leaking data in a missed spot.

### 2. Access stored in a `user_company` pivot
The table already exists since 2021 (`user_id`, `company_id`, signed, no keys) with an unused `App\Models\User\Company` model and 0 rows in production (checked 2026-10-20). It is dropped and recreated with the structure below and the dead model removed, instead of adding a second pivot.

Columns: `id`, `company_id`, `user_id`, `granted_by` (nullable user id), timestamps, unique (`company_id`, `user_id`). The pivot holds **only the extra companies** a user can access, never the default company. Regular users, the vast majority, have no rows at all.

- `User::canAccessCompany(int $companyId)`: `$companyId === default_company_id` is answered in memory; only other companies hit the pivot (one indexed lookup).
- No backfill of the pivot is needed and creating a user does not touch it.
- When a SuperAdmin changes a user's default company to one that was an extra grant, that pivot row is removed (it is now the default).

- *Alternative: also store the default company in the pivot.* Rejected: one row per existing user, a backfill, an insert on every user creation and a query for every access check, all to answer something the user row already knows.

### 3. Quota stored on the parent company, client companies linked to it
Two `company` columns:
- `managed_companies_limit` (unsigned small int, default 0): client companies the parent company may create. Edited only by SuperAdmin from the company form.
- `parent_company_id` (nullable, FK to `company.id`): set on client companies to the parent company that created them.

Used quota = client companies of the parent company **including soft deleted ones** (`withTrashed()`). Available = limit − used, never below 0. One level only: a client company never has clients of its own.

The parent company is the user's default company (decision 3b), so creating a company from inside a client company still counts against the user's own parent company.

The quota is part of the parent company's plan, so it lives on the company: it survives changes of staff, several CompanyAdmins of the parent company share it, and a future billing module only has to update one company row.

- Granting access also sets the parent: `CompanyAccessService::grant()` makes a company without parent (and without client companies) a client of the user's default company, unless that default company is itself a client company. Companies a parent company's users manage therefore always count against its quota, whether they were created by a MultiCompanyAdmin or granted by a SuperAdmin; a SuperAdmin grant may exceed the quota (shown as e.g. `4 / 3`), which only blocks new creations.
- *Alternative: quota on the user.* Rejected: harder to maintain (one number per employee instead of one per contract) and lost when the user leaves the parent company.
- *Alternative: derive access from the hierarchy (every parent company user can access every client).* Rejected for now: access stays explicit in `user_company`, the hierarchy is only used for the quota. It can be added later without data changes.

### 3b. Default company only for multi-company users: nullable `user.default_company_id`
`user.company_id` stays exactly as today. For multi-company users it becomes the **active** company and changes on every switch, so they also need a stable company they belong to. `default_company_id` (nullable FK to `company.id`):
- **Null for regular users.** No data migration, no observer, existing and new users are untouched.
- **Set when a user gets their first extra company** (SuperAdmin grant or creating their first client company) to their current `company_id`. At that moment the user has never switched, so `company_id` is their own company.
- **Cleared when the last extra company is revoked**, after moving `company_id` back to it.
- One helper resolves it: `User::defaultCompanyId()` returns `default_company_id ?? company_id`. It defines the parent company for the quota, the fallback when the active company is revoked and the company used on login.
- **Control:** `User::whereNotNull('default_company_id')` (scope `multiCompany()`) lists the users with multi-company access, so the SuperAdmin can count and audit them.
- Changed only by SuperAdmin: for a multi-company user, the company select already in the user form edits the default company.
- On web login (`LoginController::authenticated()`), `company_id` is reset to `defaultCompanyId()` inside the `update()` that already stores `last_login_at`, so it costs no extra query and a session never starts silently inside a client company.
- The API (JWT) login does **not** reset it: an integration logging in periodically would otherwise move the user back to their default company in the middle of their web work, since the active company is shared by all sessions.
- Never stored in `user_company`, so it cannot be revoked; the access select in the user form excludes it.

- *Alternative: backfill `default_company_id` for every user and make it not null.* Rejected: it touches every user row for a feature few users have, and loses the simple "not null = multi-company" indicator.
- *Alternative: a Spatie permission such as `manage multiple companies`.* Not needed: the stored default already identifies these users, and a permission could drift from the actual grants.

- *Alternative: derive the parent company from the active company (`parent ?? self`).* Rejected: it needs a rule per use case (quota, revoke fallback, login) and gives no stable answer to "which company does this user belong to".
- *Alternative: keep the last active company on login.* Rejected: starting every session in the user's own company is more predictable and avoids working in a client company by mistake.

### 4. MultiCompanyAdmin role, a CompanyAdmin with more permissions
A single role: the user is the admin of their company and can also access and create client companies.
- New role `MultiCompanyAdmin` in `RoleSeeder`. `PermissionSeeder` gives it **every permission of CompanyAdmin automatically** (`withInheritedRoles()`), plus `create company`, so new CompanyAdmin permissions are inherited without being listed twice.
- About 10 places checked the role name (`hasRole('CompanyAdmin')`: assigned seller scope, dashboard sales, sale report, payroll delete, permissions screens, tickets, negative transactions). They now use `User::isCompanyAdmin()`, backed by `User::COMPANY_ADMIN_ROLES = ['CompanyAdmin', 'MultiCompanyAdmin']`, so the role also inherits role-name checks.
- `User::ADMINISTRATOR_ROLES` includes it: only a SuperAdmin assigns it or manages its holders, and a CompanyAdmin cannot change its permissions in the permissions screen.
- Counting holders: `whereHas('roles', ...)`, which does not throw if the role is not seeded yet.
- *Alternative: assign both CompanyAdmin and MultiCompanyAdmin.* Rejected: assigning only MultiCompanyAdmin from the user form left the user without any CompanyAdmin permission; one role is simpler to understand and to count.
- *Alternative: copy CompanyAdmin permissions by hand.* Rejected: it misses the role-name checks and drifts as permissions are added.
- *Alternative: give `create company` to every CompanyAdmin.* Rejected: creating companies is a paid capability; the role makes it explicit and auditable.

### 4b. Company creation by MultiCompanyAdmin
- A policy-style check `User::canCreateCompany()`: SuperAdmin always; MultiCompanyAdmin only when their default company has available quota. It is used by the create route, `CompanySaveRequest::authorize()` (for requests without id) and the view to hide the button.
- When a MultiCompanyAdmin creates a company, `CompanySaveController` sets `parent_company_id` to the parent company, then grants the creator access (`granted_by` = creator), all in one DB transaction with a `lockForUpdate` on the parent company row, so two concurrent creations cannot exceed the quota and a failure never leaves a company counted but inaccessible. Companies created by SuperAdmin have no parent unless the SuperAdmin picks one.
- `managed_companies_limit` and `parent_company_id` are only accepted from SuperAdmin in `CompanySaveRequest`; for anyone else they are ignored.
- The creator is **not** switched automatically to the new company; they switch from the navbar. This keeps the save flow identical to today's.

### 5. Switch endpoint
`POST /company/switch/{company}` (`CompanySwitchController`), behind `auth` and `locked`:
1. Check `canAccessCompany()` and that the company is not soft deleted; otherwise 403 and log `company_switch_denied`.
2. Update `company_id`, log `company_switched` with `from_company_id` and `to_company_id`.
3. Redirect to `/` (dashboard), because the previous page may point to a record of the old company.

The navbar dropdown is rendered only when `default_company_id` is not null, a check on the already loaded user, so regular users never read the cache or the database for it. The list (id and name of default plus extra, non deleted companies) is cached per user with `Cache::remember("user.{id}.companies")` and forgotten whenever that user's grants change, a client company is created or a company is deleted/restored. For multi-company users the switcher costs one cache read per page.

### 6. Access management by SuperAdmin
In the user form (only for SuperAdmin): a multi select of companies the user can access. The quota is edited in the company form. Saving syncs the pivot (`granted_by` = SuperAdmin for new rows) and logs `company_access_granted` / `company_access_revoked`. Validation rejects an empty list. If the active company is revoked, `company_id` moves back to `default_company_id`; revoking the default company is rejected.

### 7. Company list scoping
`CompanyIndexRequest` opens the list to SuperAdmin, CompanyAdmin and MultiCompanyAdmin (today SuperAdmin only); the deleted/all filter stays SuperAdmin only. `Company::getAllPaginated()` gets an optional user: for non SuperAdmin users the query is limited to their default company plus their extra companies (`grantedUsers`), so a single company admin sees one row. `CompanyUpdateRequest` and `CompanySaveRequest` share `User::canUpdateCompany()`: non SuperAdmin users can open and save any company they can access, without switching (today the form of any company could be opened, since only the permission was checked); the edit link is only shown on those rows.

### 8. Self management and role safety
- Every user can manage themselves (`canManageUser()`), and `User::manageableBy()` finds them even while working in a client company.
- Users change their own data without touching their roles (`canChangeRolesOf()`): the role selector is hidden on their own user and submitted roles are ignored; only SuperAdmin changes their own roles.
- When an administrator edits another user, roles the administrator cannot assign (SuperAdmin, MultiCompanyAdmin for a CompanyAdmin) are kept by `rolesToSync()`, so saving a form never strips them.
- Deleting users uses `canManageUser()`, so a CompanyAdmin cannot delete a MultiCompanyAdmin.

## Risks / Trade-offs

- [Switching in one tab changes the company in all tabs and devices] → Show the active company name in the navbar at all times; redirect to the dashboard after switching. Documented as a known limitation.
- [A multi-company user working in client company B could be listed and managed (password, deactivation) by B's CompanyAdmin, since user management filtered by `company_id`] → User management (`/user` list, edit, save, delete and `canManageUser()`) uses the `belongingTo()` scope, `COALESCE(default_company_id, company_id)`, so only the admins of the user's default company manage them. Seller dropdowns and reports still use the active company, acceptable since these users are usually not sellers.
- [Spatie `User::role()` throws when the role does not exist yet (seeders not run)] → Counts use `whereHas('roles', ...)` and checks use `hasRole()`, which never throw.
- [A request already running when the user switches finishes with the old company] → Every request reads `company_id` once at the start; no mixed writes inside one request.
- [`default_company_id` out of sync with the grants] → It is only written in one service (`CompanyAccessService`) used by every grant, revoke and client company creation, covered by tests for first grant, last revoke and creation.
- [Stale switcher cache after a grant change] → Every write path (grant/revoke, company create, delete, restore) forgets the affected users' keys; the switch endpoint always re-checks access in the database, so a stale cache can only show or hide a menu entry, never grant access.
- [Performance regression for regular users] → Covered by a test asserting a single-company user's dashboard request runs the same number of queries as before.
- [Quota abuse by deleting and recreating companies] → Only SuperAdmin can delete companies, and soft deleted client companies still count against the parent company's quota.
- [A client company of parent company F later hires another parent company] → Out of scope; a SuperAdmin can change `parent_company_id` from the company form.

## Migration Plan

1. Deploy migrations: create `user_company`, add `company.managed_companies_limit`, `company.parent_company_id` and `user.default_company_id`, (nullable, no data migration).
2. Run `php artisan db:seed --class=RoleSeeder` and `--class=PermissionSeeder` (both idempotent) to create `MultiCompanyAdmin` with `create company`.
3. SuperAdmin assigns `MultiCompanyAdmin` to the user and sets the quota of the parent company from the company form.

Rollback: drop the switch route and views, roll back the migrations (users keep their current `company_id`, which is still valid), delete the `MultiCompanyAdmin` role.
