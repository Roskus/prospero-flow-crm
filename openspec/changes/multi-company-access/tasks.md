# Tasks

## 1. Data model

- [x] 1.1 Create migration that drops the legacy empty `user_company` table and recreates it (FKs to `user`/`company`, `company_id`, `user_id`, nullable `granted_by`, timestamps, unique `company_id` + `user_id`) and remove the unused `App\Models\User\Company` model; verify with `php artisan migrate`, `migrate:rollback` and the `database-schema` tool
- [x] 1.2 Create migration adding `company.managed_companies_limit` (unsigned small integer, default 0) and nullable `company.parent_company_id` (FK to `company.id`), and nullable `user.default_company_id` (FK to `company.id`), and verify the columns with the `database-schema` tool
- [x] 1.3 Add `User::defaultCompanyId()` (`default_company_id ?? company_id`) and the `multiCompany()` scope (`default_company_id` not null); verify with tests that regular users resolve to their company and are not in the scope
- [x] 1.4 Add `User::extraCompanies()`, `Company::grantedUsers()` (belongsToMany with `granted_by` and timestamps; `Company::users()` already exists for own users), `Company::parent()` and `Company::clientCompanies()` relations, plus factory states (`clientOf($parent company)`, `withQuota($n)`), and verify with a model test
- [x] 1.5 Add `User::defaultCompany()` relation, `Company::usedClientQuota()` (client companies including soft deleted), `availableClientQuota()`, plus `User::canAccessCompany(int $companyId)` (default company answered in memory, others through the pivot) and `canCreateCompany()` (SuperAdmin always, MultiCompanyAdmin only when their default company has available quota); verify with feature tests covering limit 0, partial use, full use, deleted clients counting and quota lowered below usage
- [x] 1.6 Create `CompanyAccessService` with `grant()` (stores `default_company_id` on the first extra company), `revoke()` (moves `company_id` back to the default company if it was active, clears `default_company_id` after the last extra company) and cache invalidation; verify with tests for first grant, second grant, revoking the active company, revoking the last extra company and that creating users leaves `default_company_id` null

## 2. Company switching

- [x] 2.1 Reset `company_id` to `defaultCompanyId()` on web login (in the existing `update()` of `LoginController::authenticated()`), not on API login; verify with tests that a user logged out inside a client company logs in back in their default company and that an API login keeps the active company
- [x] 2.2 Create `CompanySwitchController` and `POST /company/switch/{company}` route (auth, locked); reject companies without access or soft deleted with 403 and log `company_switch_denied`; on success update `company_id`, log `company_switched` (from/to) and redirect to `/`; verify with feature tests for allowed, no access, deleted company and security log entries
- [x] 2.3 Add the navbar company switcher in `layouts/app.blade.php`, fed by the per-user cached company list (forgotten on grant changes, company create, delete and restore) and shown only when `default_company_id` is not null, marking the active one; add translations to `lang/en.json` and `lang/es.json`; verify with view tests for single and multi company users
- [x] 2.4 Verify data isolation after a switch with a feature test: leads/customers/orders lists show only the active company data, and a lead created after switching belongs to the new company with the real user as creator
- [x] 2.5 Verify with a query log test that a single-company user's dashboard request runs no `user_company` or client quota queries

## 3. Company creation within quota

- [ ] 3.1 Add the `MultiCompanyAdmin` role to `RoleSeeder` with only `create company` in `PermissionSeeder`, and make it assignable/manageable only by SuperAdmin (`assignableRoles()`, `canManageUser()`); verify seeders stay idempotent, a CompanyAdmin is not offered the role and gets an error assigning it, and a user with CompanyAdmin + MultiCompanyAdmin keeps CompanyAdmin permissions
- [ ] 3.2 Use `canCreateCompany()` in the create route and in `CompanySaveRequest::authorize()` for new companies; verify MultiCompanyAdmin with quota can create, without quota gets 403, plain CompanyAdmin with quota gets 403, Seller gets 403 and SuperAdmin is never limited
- [ ] 3.3 In `CompanySaveController`, for MultiCompanyAdmin creations set `parent_company_id` to the creator's `defaultCompanyId()` and grant the creator access through `CompanyAccessService` in one transaction locking the parent company row; verify the new company is a client of the parent company (also when created while active in a client company), the creator can switch to it and the parent company's quota decreases
- [ ] 3.4 Hide the create company button when `canCreateCompany()` is false and show the parent company's available quota to MultiCompanyAdmin; verify with view tests
- [x] 3.5 Add `managed_companies_limit` and parent company fields to the company form, accepted only from SuperAdmin in `CompanySaveRequest`, with translations; verify SuperAdmin can set them and a CompanyAdmin posting them changes nothing

## 4. Company list scoping

- [x] 4.1 Open `CompanyIndexRequest` to CompanyAdmin and MultiCompanyAdmin (filter SuperAdmin only) and scope `Company::getAllPaginated()` to their accessible companies; verify CompanyAdmin with and without extra companies, Seller/Support 403 and SuperAdmin sees all
- [x] 4.2 Restrict `CompanyUpdateRequest` to the active company for non SuperAdmin users and show the edit button only on that row; verify opening an accessible but inactive company form is rejected

## 5. Access management by SuperAdmin

- [x] 5.1 Add the accessible companies multi select to the user form, visible only to SuperAdmin, with translations; verify with view tests for SuperAdmin and CompanyAdmin
- [x] 5.2 Validate and save it in `UserRequest`/`UserSaveController`: ignore the field for non SuperAdmin, exclude the default company, apply the changes through `CompanyAccessService` with `granted_by`, forget the user's switcher cache, log `company_access_granted`/`company_access_revoked`; verify with feature tests
- [ ] 5.3 Show the SuperAdmin how many users have the MultiCompanyAdmin role and how many have extra companies (`multiCompany()`); verify with a feature test

## 6. Integration

- [ ] 6.1 Run `vendor/bin/pint --dirty --format agent` and the full test suite (`php artisan test --compact`) and verify both pass
- [ ] 6.2 Manually verify the multi-company flow in the browser: SuperAdmin assigns MultiCompanyAdmin and sets the parent company's quota, the user creates a client company, switches to it, works there and switches back
