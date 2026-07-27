## Context

Prospero Flow CRM is a multitenant Laravel 13 application managing sales, customers, and relationships. Currently, contracts are not tracked within the system—sales teams handle contract management through external tools (documents, spreadsheets). This creates information silos, makes renewal tracking manual, and complicates financial forecasting.

The contract module will centralize contract data, integrate with existing Customers, Contacts, and Opportunities, and provide visibility into contract status, financials, and renewal timelines. Each contract belongs to exactly one Customer (1:Many relationship). Constraints: must follow Laravel 13 conventions, integrate with existing auth/policies, maintain company-scoping for multitenant isolation, and maintain performance with large contract volumes.

## Goals / Non-Goals

**Goals:**
- Provide full contract lifecycle management (creation through renewal/expiration)
- Track contract financials (values, discounts, taxes, line items) with accurate totals
- Link contracts to Accounts, Contacts, Opportunities, and Users
- Enable filtering and searching contracts by status, date, and related entities
- Send renewal reminders based on configurable renewal reminder dates
- Support contract versioning or amendment tracking
- Provide API endpoints for third-party integrations

**Non-Goals:**
- Electronic signature integration (e-sign workflows) — contract signing dates are manual entry only
- Contract template system — templates are out of scope for MVP
- Advanced reporting/analytics dashboards — basic CRUD and listing only
- Document upload/storage — contract documents are referenced externally
- Bulk contract import — manual entry or single-file import outside this scope

## Decisions

### 1. Data Model: Contracts as Aggregates with Line Items

**Decision:** Implement contracts using two related tables: `contracts` (parent) and `contract_line_items` (children).

**Rationale:** Contract financials require line-level granularity for discounts, taxes, and shipping. Storing totals on the contract allows fast aggregation, while line items enable detailed tracking. This mirrors accounting patterns and supports future invoice/billing integrations.

**Alternatives Considered:**
- Flat contract table with financial fields only — simpler but loses line-level audit trail and prevents future features like line-item-level taxes
- Separate `ContractLineItem` model with JSON field for line items — rejected because relational model is more queryable and supports filtering

### 2. Financial Calculations: Stored vs Computed

**Decision:** Store line-item-level data (Subtotal, Discount, Tax, Shipping, Shipping Tax) and compute Grand Total on the fly using a `calculateTotals()` method.

**Rationale:** Line items require persistence for accuracy and audit. Grand Total is derived from line items, so computing it avoids denormalization bugs. Caching totals in contracts table for query performance only if performance testing shows it's needed.

**Alternatives Considered:**
- Store all totals in contracts table — requires sync logic and is error-prone
- Compute everything on demand — slow for listing pages with many contracts

### 3. Contract Status Enum

**Decision:** Use a PHP Enum for contract status: `Draft | Active | Expired | Renewed | Cancelled`

**Rationale:** Enums provide type safety, prevent invalid states, and make queries explicit. Matches Laravel 13 conventions.

**Alternatives Considered:**
- String field with enum constraint in DB — less type-safe in PHP code
- Status history table — over-engineered for MVP; simple status field sufficient for initial release

### 4. Relationships: BelongsTo for ForeignKeys

**Decision:** 
- Contract BelongsTo Customer (required) - 1:Many relationship
- Contract BelongsTo Contact (optional, nullable)
- Contract BelongsTo Opportunity (optional, nullable)
- Contract BelongsTo User (as contract_manager_id)

**Rationale:** Customer is the primary business entity; each contract belongs to exactly one customer. Contact and Opportunity are optional for flexibility. User assignment (Contract Manager) enables notifications and responsibility tracking.

**Alternatives Considered:**
- ManyToMany with Customers — contracts belong to one customer; ManyToMany adds unnecessary complexity

### 5. Renewal Reminders: Date-Based Trigger

**Decision:** Store `renewal_reminder_date` on contracts table. Use a scheduled artisan command (daily) to check for upcoming renewals and send notifications.

**Rationale:** Simple, decoupled from request/response cycle, and follows existing app patterns for notifications.

**Alternatives Considered:**
- Job queue for individual contracts — adds complexity; daily batch job is sufficient
- Webhook-based third-party reminders — out of scope for MVP

### 6. API Endpoints: RESTful with Nested Resources

**Decision:** Implement routes:
- `GET/POST /contract` — list and create
- `GET/PATCH/DELETE /contract/{id}` — view, update, delete
- `POST /contract/{id}/line-items` — add line items (nested)
- `PATCH /contract/{id}/line-items/{lineId}` — update line item
- `DELETE /contract/{id}/line-items/{lineId}` — remove line item

**Rationale:** Standard REST conventions; nested line-item endpoints keep related data grouped.

### 7. Authorization: Policy-Based with Company Scoping

**Decision:** Implement `ContractPolicy` with gates for view, create, update, delete. Scope all queries by company (tenant) to prevent cross-company data leakage.

**Rationale:** Aligns with existing app security model (company scoping for multi-tenant safety).

## Risks / Trade-offs

| Risk | Mitigation |
|------|-----------|
| Financial calculations (discounts, taxes) are complex and error-prone | Add comprehensive unit tests for calculation logic; use database transactions for consistency |
| Large contract portfolios (1000s of contracts) may slow list queries | Index on status, account_id, created_at; implement pagination and filtering; monitor query performance |
| Renewal reminder dates may be missed if daily job fails | Log job execution; add monitoring/alerts for failed jobs; consider retry logic |
| Contract signing dates are manual entry; no audit trail of who entered them | Document that signing dates are manually verified; consider adding updated_by tracking if audit is critical |
| Line items UI could become complex with many line types | Start simple (single line type); add line-type differentiation later if needed |

## Migration Plan

1. **Phase 1: Database & Model Setup**
   - Create `contract` and `contract_line_item` tables
   - Create `Contract` and `ContractLineItem` models, factories, seeders

2. **Phase 2: CRUD & API**
   - Implement controllers, routes, and Eloquent Resource classes
   - Add form validation via FormRequest classes
   - Write feature tests for happy paths and edge cases

3. **Phase 3: Frontend**
   - Blade templates for contract listing, create/edit forms
   - Add relationship selectors (Account, Contact, Opportunity, Manager)
   - Implement financial calculations in forms (JavaScript + backend validation)

4. **Phase 4: Notifications & Commands**
   - Build renewal reminder command
   - Schedule in `routes/console.php`
   - Test notification delivery

5. **Phase 5: Testing & Polish**
   - Run full test suite
   - Performance testing for contract listing
   - Security review (authorization, input validation)

**Rollback Strategy:**
- If critical bugs found: disable routes in `bootstrap/app.php`, roll back migration to remove tables, revert changes to related models

## Open Questions

- Should contracts support soft deletes (SoftDeletes trait)? Current: No, hard delete only. Revisit if audit trail becomes a requirement.
- Should line-item taxes be applied per-item or globally? Current: Per-item for flexibility. Confirm in specs.
- What email template/notification should renewal reminders use? Defer to notification implementation phase.
