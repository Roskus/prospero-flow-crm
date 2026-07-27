## Why

Prospero Flow CRM requires a comprehensive contract management module to track customer agreements, manage contract lifecycles, and monitor financial terms. This capability is essential for sales and account management teams to ensure contract compliance, track renewal dates, and maintain visibility over contractual obligations and revenue.

## What Changes

- New Contract model with full lifecycle management (creation, signing, renewal, expiration)
- Contract status tracking (Draft, Active, Expired, Renewed, Cancelled)
- Financial tracking including line items, discounts, taxes, and totals
- Relationships to Customers, Contacts, Opportunities, and Users
- Contract reminder system for renewal dates
- Contract document management and signing date tracking
- Contract types and classification
- API endpoints for CRUD operations
- Blade templates for contract forms and listing views

## Capabilities

### New Capabilities

- `contract-management`: CRUD operations for contracts with full lifecycle (creation, active status, expiration, renewal)
- `contract-relationships`: Link contracts to Customers, Contacts, Opportunities, and assign Contract Managers
- `contract-financial-tracking`: Manage contract values, line items, discounts, taxes, shipping, and calculate totals
- `contract-renewal-tracking`: Track renewal reminder dates and contract renewal workflow
- `contract-signing-workflow`: Record customer and company signing dates for contracts

### Modified Capabilities

- `customer-management`: Customers will now have a relationship to contracts (1 Customer → N Contracts) for viewing associated contracts
- `contact-management`: Contacts will have a relationship to contracts for visibility of related contacts
- `opportunity-management`: Opportunities can link to contracts for tracking conversion to active contracts

## Impact

**Code & Database:**
- New Contract model, migration, factory, seeder
- New ContractLineItem model for line-level tracking
- Contract policy for authorization
- New database tables: contracts, contract_line_items

**APIs:**
- RESTful contract endpoints (GET, POST, PUT, DELETE)
- Contract listing with filters (status, customer, date range)
- Line items sub-resource

**Frontend:**
- Contract create/edit forms with financial calculations
- Contract listing and detail views
- Relationship selectors for Customer, Contact, Opportunity, Manager
- Date pickers for dates and renewal reminders

**Relationships:**
- Contract → Customer (BelongsTo) - required, 1:Many
- Contract → Contact (BelongsTo, nullable)
- Contract → Opportunity (BelongsTo, nullable)
- Contract → User (BelongsTo, as manager)
- Contract → LineItems (HasMany)
