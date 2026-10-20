# Proposal

## Why

Some customers manage the CRM of several client companies. Today a user belongs to exactly one company (`user.company_id`), so the SuperAdmin has to create a separate account per client company and the user has to log in and out to move between them. We need a way for one user to manage several companies with their own identity, and a quota the SuperAdmin controls so this can later be offered as a paid plan.

## What Changes

- A user can be granted access to additional companies through a new `user_company` pivot table (who granted it and when).
- The user's current `company_id` becomes the **active company**. A company switcher in the navbar lets users with access to more than one company change it; every switch is validated against the pivot and logged in the security log.
- The SuperAdmin sets on a company (for example the parent company) a quota (`managed_companies_limit`) of client companies it may create. Client companies are linked to the parent company that created them (`parent_company_id`). The SuperAdmin can also grant and revoke user access manually.
- New `MultiCompanyAdmin` role, assigned only by SuperAdmin: a CompanyAdmin with more permissions. It inherits every CompanyAdmin permission and adds accessing and creating client companies, so these users are easy to identify and count.
- A MultiCompanyAdmin whose parent company has available quota can create client companies themselves; they count against the parent company's quota. The creator gets access to the new company automatically and keeps their roles there (roles stay global, no per-company roles).
- The company list, today SuperAdmin only, shows the companies quota (used / limit).
- No impersonation: actions are always recorded under the real user.
- Billing is out of scope: the quota is a plain number set by the SuperAdmin; a private billing module may adjust it later.

## Capabilities

### New Capabilities
- `multi-company-access`: granting a user access to several companies, the per-company quota of client companies, creating client companies within the quota, switching the active company, and scoping the company list to accessible companies.

### Modified Capabilities

## Impact

- **Database**: new `user_company` table holding only extra companies (empty for existing users); new `company.managed_companies_limit`, `company.parent_company_id` and nullable `user.default_company_id` columns. `default_company_id` is only filled for users with multi-company access, so existing users are not migrated and it also counts how many users have this access.
- **Performance**: users with a single company get no extra queries per request; the switcher list is cached per user.
- **Models**: `User` (companies relation, access check, create check), `Company` (users, parent and client companies relations, quota helpers).
- **Controllers/routes**: new company switch endpoint; SuperAdmin management of user access (user form) and of the quota (company form); `CompanySaveController`/`CompanySaveRequest` and `CompanyIndexController` updated; `create company` permission given to the new `MultiCompanyAdmin` role, guarded by quota; `RoleSeeder`, `PermissionSeeder` and `User::assignableRoles()` updated.
- **Views**: navbar company switcher in `layouts/app.blade.php`; access management in the user form; quota in the company form; company list.
- **Security**: `SecurityLogger` events for switches and access changes. All existing queries keep filtering by `Auth::user()->company_id`, so data isolation does not change.
- **API (JWT)**: API requests use the same active company as the web session.
- **Translations**: new keys in `lang/en.json` and `lang/es.json`.
