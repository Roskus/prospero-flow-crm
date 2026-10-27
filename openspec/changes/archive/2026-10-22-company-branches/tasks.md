# Tasks

## 1. Data model

- [x] 1.1 Create the `branch` migration (`company_id` FK cascade on delete, `name`, `code` varchar(10), `is_main` boolean default false, `$table->address()`, nullable `phone`, `email`, timestamps, soft deletes, unique `company_id` + `code`) and verify with `php artisan migrate`, `migrate:rollback` and the `database-schema` tool
- [x] 1.2 Create the `Branch` model (`HasAddress`, casts, `company()` relation, `ofCompany` scope; soft deleted branches are already excluded by `SoftDeletes`) with factory states (`main()`, `forCompany()`), plus `Company::branches()`, `Company::mainBranch()` and `Company::hasSeveralBranches()`; verify with a model test
- [x] 1.3 Create the data migration that inserts a main branch (code `0001`, company name, copied address/phone/email) for every company including soft deleted ones, using the query builder and skipping companies that already have one; verify with a migration test on companies with and without address and that running it twice creates no duplicates
- [x] 1.4 Create `BranchService` with `createMainBranch(Company)` (also assigns the new main branch to the company users whose `default_branch_id` is null, `default_company_id ?? company_id`), `makeMain(Branch)` (transaction + company row lock, reloads the target and refuses a deleted branch, clears the previous main), `delete(Branch)` (same lock, reloads the branch and refuses it when it is main) and `nextCode(Company)` (max numeric code + 1, 4 digits); call `createMainBranch()` from `CompanyObserver::created()`; verify with tests that companies created through `CompanyRepository`, the MultiCompanyAdmin flow and the `company:create` command get exactly one main branch, that `makeMain()` leaves exactly one main, that `makeMain()` refuses a deleted branch, that `delete()` refuses a branch that is main when reloaded and that `nextCode()` skips non numeric codes and counts deleted branches

## 2. User default branch

- [x] 2.1 Create the migration adding nullable `user.default_branch_id` (FK to `branch`, null on delete) and backfilling every user with the main branch of their `default_company_id ?? company_id` in a separate data migration; verify with the `database-schema` tool and a migration test
- [x] 2.2 Add `User::defaultBranch()` and `User::currentBranch()` (default branch when it is an active branch of the active company, otherwise the active company's main branch, memoized per request) and a `creating` hook that sets `default_branch_id` to the company's main branch when empty; verify with tests for a new user, a user with another default branch, a deleted default branch, a multi-company user switched to a client company and that changing the main branch does not change existing users and that a user created before their company's main branch gets it stored once the branch is created
- [x] 2.3 Add the default branch select to the user form (`UserSaveController` and its request), limited to active branches of the user's default company and validated with `Rule::exists()` scoped to that company; verify with feature tests for saving a valid branch and rejecting a branch of another company

## 3. Branch management

- [x] 3.1 Create `routes/module/branch.php` with index (`read company`), create/update/save/delete/set main (`update company`) and the controllers plus `BranchSaveRequest` (required name and code, code unique per company including deleted, `AddressRules::rules()`); verify with feature tests for listing only the active company branches, 404 on another company branch, 403 without `update company` and the duplicated code error
- [x] 3.2 Create the branch list (name, code, inline address with the display component, phone, main badge, actions) and form views (address form component) following the existing CRUD views, with the code prefilled with `BranchService::nextCode()`; verify with a view test for the suggested code and the main badge
- [x] 3.3 Implement delete through `BranchService::delete()` (soft delete, refused for the main branch with a flash error) and "Set as main" actions; verify with feature tests that the main branch cannot be deleted, a deleted branch disappears from selects and users of the deleted branch resolve to the main branch
- [x] 3.4 Add the Branches tile to the Organization group in `setting/index.blade.php` (visible with `read company`) and a hint in the company form linking to branches; add all translations to `lang/en.json` and `lang/es.json`; verify by extending `SettingIndexControllerTest` and running `RrhhTranslationTest`-style key checks for the branch views

## 4. Order branch

- [x] 4.1 Create the migration adding `order.branch_id` (FK to `branch`), backfilling it with one `UPDATE ... JOIN` to each company's main branch and making it not null (three migrations: add, backfill, require); add `Order::branch()` (loading trashed branches); verify with the `database-schema` tool and a migration test
- [x] 4.2 Default the branch in `OrderObserver::creating()` (creator's current branch, else the company's main branch) and accept an optional `branch_id` in `OrderSaveRequest`, `OrderRepository` and the API order create/update requests, validated against active branches of the active company; add `branch_id` to the order OpenAPI schema and API responses; verify with web and API tests for the default branch, a chosen branch and a branch of another company
- [x] 4.3 Show the branch select in the order form and the branch column and filter in the order list only when `Company::hasSeveralBranches()`; verify with feature tests for a single branch company (nothing shown) and a two branch company (column shown, filter returns only that branch's orders)

## 5. Integration checks

- [x] 5.1 Run `php artisan migrate:fresh --seed` and the order, company, user and setting test suites; verify seeded companies, users and orders all have a branch and the suites pass
