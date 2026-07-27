## 1. Database & Model Setup

- [ ] 1.1 Create migration for contracts table with all required columns (title, status, value, currency, dates, relationships, etc.)
- [ ] 1.2 Create migration for contract_line_items table with line-level financial tracking
- [ ] 1.3 Create Contract model with proper casts (Status enum, dates)
- [ ] 1.4 Create ContractLineItem model and relationships
- [ ] 1.5 Add relationship methods to Contract model (customer, contact, opportunity, manager, lineItems)
- [ ] 1.6 Create ContractFactory with realistic test data
- [ ] 1.7 Create ContractSeeder for initial data
- [ ] 1.8 Run migrations and verify database schema

## 2. Authorization & Policies

- [ ] 2.1 Create ContractPolicy with view, create, update, delete gates
- [ ] 2.2 Add company-scoping to all contract queries via policy
- [ ] 2.3 Create FormRequest classes for contract validation (CreateContractRequest, UpdateContractRequest)
- [ ] 2.4 Create FormRequest for line items (CreateLineItemRequest, UpdateLineItemRequest)
- [ ] 2.5 Add validation rules for financial fields (non-negative, numeric)

## 3. API & Controllers

- [ ] 3.1 Create ContractController with index, show, store, update, destroy actions
- [ ] 3.2 Create ContractLineItemController with store, update, destroy actions for nested routes
- [ ] 3.3 Create Eloquent API Resource classes (ContractResource, ContractLineItemResource)
- [ ] 3.4 Register API routes for contracts (/api/contracts, nested /line-items)
- [ ] 3.5 Implement filtering (by status, customer, date range, manager) in ContractController
- [ ] 3.6 Implement pagination on contracts listing
- [ ] 3.7 Implement search by contract title in listing
- [ ] 3.8 Add financial calculation methods to Contract model (calculateTotals, subtotal, grandTotal, etc.)

## 4. Blade Templates & Frontend

- [ ] 4.1 Create contracts index (listing) view with table, filters, pagination, search
- [ ] 4.2 Create contract create/edit form template
- [ ] 4.3 Add form fields for all contract attributes (title, status, dates, financial, relationships)
- [ ] 4.4 Add relationship selectors (Account dropdown required, Contact/Opportunity/Manager optional)
- [ ] 4.5 Create contract detail/show view with all contract information
- [ ] 4.6 Create line items section in contract forms (add/edit/delete line items)
- [ ] 4.7 Implement financial calculation display (Subtotal, Discount, Tax, Shipping, Grand Total)
- [ ] 4.8 Add JavaScript for real-time financial calculations in forms
- [ ] 4.9 Create contract signing status badge/indicator
- [ ] 4.10 Create related records display sections (Account, Contact, Opportunity, Manager links)

## 5. Relationship Integration

- [ ] 5.1 Add hasMany('contracts') relationship to Account model
- [ ] 5.2 Add hasMany('contracts') relationship to Contact model
- [ ] 5.3 Add hasMany('contracts') relationship to Opportunity model
- [ ] 5.4 Add hasMany('contracts') relationship to User model (as contract manager)
- [ ] 5.5 Add contracts section to Account detail view
- [ ] 5.6 Add contracts section to Contact detail view
- [ ] 5.7 Add contracts section to Opportunity detail view
- [ ] 5.8 Add "Create Contract" buttons to Account, Contact, Opportunity detail views with pre-linking
- [ ] 5.9 Add filters for contracts by status in related model detail views

## 6. Renewal Tracking

- [ ] 6.1 Create SendContractRenewalReminders artisan command
- [ ] 6.2 Implement renewal reminder logic (check date, send notification)
- [ ] 6.3 Create notification class for renewal reminders
- [ ] 6.4 Register renewal reminder command in routes/console.php scheduler
- [ ] 6.5 Add renewal reminder filter to contracts listing (contracts approaching renewal)
- [ ] 6.6 Add renewal reminder date picker to contract forms
- [ ] 6.7 Test renewal notification delivery

## 7. Financial Features

- [ ] 7.1 Implement Contract::calculateSubtotal() method
- [ ] 7.2 Implement Contract::applyDiscount() with percentage and fixed amount support
- [ ] 7.3 Implement Contract::applyTax() method
- [ ] 7.4 Implement Contract::calculateGrandTotal() method with all components
- [ ] 7.5 Add line-item level financial calculations
- [ ] 7.6 Add Subtotal, Discount, Tax columns to contract listing view
- [ ] 7.7 Display Grand Total prominently in contract listing and detail views
- [ ] 7.8 Validate financial fields (non-negative, numeric) on both client and server

## 8. Testing

- [ ] 8.1 Create feature tests for contract CRUD operations
- [ ] 8.2 Create feature tests for contract authorization (view, create, update, delete)
- [ ] 8.3 Create feature tests for contract relationships (linking to Account, Contact, etc.)
- [ ] 8.4 Create feature tests for financial calculations and totals
- [ ] 8.5 Create feature tests for line items (add, update, delete)
- [ ] 8.6 Create unit tests for financial calculation methods
- [ ] 8.7 Create feature tests for renewal reminders
- [ ] 8.8 Create tests for contract filtering and search
- [ ] 8.9 Test company scoping (cross-company data isolation)
- [ ] 8.10 Run full test suite and verify all tests pass

## 9. Code Quality & Documentation

- [ ] 9.1 Run pint (PHP formatting) on all new code
- [ ] 9.2 Verify no TypeScript/React type errors (if applicable)
- [ ] 9.3 Add PHPDoc comments to Contract and ContractLineItem models
- [ ] 9.4 Add PHPDoc comments to ContractPolicy and ContractController
- [ ] 9.5 Document API endpoints in code comments or external docs
- [ ] 9.6 Review code for security vulnerabilities (SQL injection, XSS, authorization)

## 10. Deployment & Final Verification

- [ ] 10.1 Verify all migrations run cleanly on fresh database
- [ ] 10.2 Verify no console errors or warnings in development
- [ ] 10.3 Test contract creation, viewing, updating, deletion in browser
- [ ] 10.4 Test financial calculations with various discount/tax scenarios
- [ ] 10.5 Test renewal reminder command runs without errors
- [ ] 10.6 Test relationships from Account/Contact/Opportunity detail pages
- [ ] 10.7 Verify authorization (admin can see all, users see only own company)
- [ ] 10.8 Performance test: list 1000 contracts, verify load time acceptable
- [ ] 10.9 Create sample data/documentation for contract module usage
- [ ] 10.10 Mark change as complete and ready for review
