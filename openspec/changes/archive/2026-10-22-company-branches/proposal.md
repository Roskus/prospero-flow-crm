# Proposal

## Why

A company may operate from several locations (branches) that share its legal identity (business name, VAT) but have their own address and phone. Today everything hangs directly from the company, so there is no way to know from which location an order was made. The upcoming accounting and invoicing change needs branches from the start: invoice numbering per branch (point of sale / invoice series), one cash account per branch and the issuing branch address on the invoice PDF.

## What Changes

- New `branch` entity belonging to a company: name, code, address (the reusable address from `reusable-address`, with map), phone and email, with soft deletes.
- Every company has exactly one **main branch**. It is created automatically with the company address when a company is created, and existing companies get one through a migration. The main branch cannot be deleted; another branch can be made the main one.
- Branch management screen (list, create, edit, delete, set as main) for users who can update the company, reachable from Settings → Organization.
- Every user has a **default branch** (`default_branch_id`), set to the main branch of their company when the user is created; existing users get their company's main branch through a migration. When the default branch is deleted or not in the active company (multi-company users), the active company's main branch is used.
- Orders belong to a branch. New orders take the user's current branch and the order form lets the user change it when the company has more than one branch. Existing orders are assigned to their company's main branch.
- Branches are not separate legal entities: multi-company access (`company`, `parent_company_id`) is unchanged.

## Capabilities

### New Capabilities
- `company-branches`: branches of a company, the main branch, branch management, the user's default branch and the branch an order belongs to.

### Modified Capabilities

## Impact

- **Database**: new `branch` table; `user.default_branch_id` and `order.branch_id` (FK to `branch`). Data migration creating a main branch per existing company and assigning existing users and orders to it.
- **Models**: new `Branch` model and factory; `Company` (`branches()`, `mainBranch()`), `User` (`defaultBranch()`, `currentBranch()`), `Order` (`branch()`).
- **Observers**: `CompanyObserver::created()` creates the main branch and the user creation sets the default branch, so companies created from the UI, by a MultiCompanyAdmin or with the `company:create` command all get one.
- **Controllers/routes**: new branch CRUD under `routes/module/branch.php`, authorized with the existing `read company` / `update company` permissions; `OrderRepository` and the order form set the branch; user form gets a default branch select.
- **Depends on**: `reusable-address` (address columns macro, `HasAddress`, address form and display components).
- **Views**: branch list and form (address form component), Settings → Organization tile, branch select in order and user forms.
- **Translations**: new keys in `lang/en.json` and `lang/es.json`.
- **Next change**: `accounting-invoicing` builds on this (invoice series and cash account per branch).
