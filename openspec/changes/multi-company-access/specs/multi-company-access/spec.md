# Spec Delta

## Purpose

Lets one user manage several companies with their own identity: the SuperAdmin grants access and gives a company a quota of client companies, the user creates client companies within that quota and switches the active company they work on.

## ADDED Requirements

### Requirement: Company access grants
The system SHALL keep, for every user, the set of companies the user can access. A user SHALL always have access to their default company. Only a SuperAdmin SHALL grant or revoke a user's access to other companies. Every grant SHALL record who granted it and when.

#### Scenario: New user has access to their own company
- **WHEN** a user is created in company A
- **THEN** the user can access company A and no other company

#### Scenario: SuperAdmin grants access
- **WHEN** a SuperAdmin grants a user access to company B
- **THEN** the user can access company B
- **AND** the grant records the SuperAdmin as the grantor

#### Scenario: Non SuperAdmin cannot grant access
- **WHEN** a CompanyAdmin tries to grant or revoke a user's access to a company
- **THEN** the request is rejected with a forbidden error
- **AND** the user's access does not change

#### Scenario: Default company cannot be revoked
- **WHEN** a SuperAdmin tries to revoke a user's access to the user's default company
- **THEN** the request is rejected with a validation error

### Requirement: Default company
Every user SHALL have one default company: the company they belong to. For users without extra companies the default company SHALL be their company and no separate default SHALL be stored, so their data stays exactly as today. When a user gets their first extra company, the system SHALL store their current company as their default company. When a user loses their last extra company, the stored default SHALL be cleared and their company SHALL be their default company again. Only a SuperAdmin SHALL change a user's default company. When a user logs in on the web, their active company SHALL be their default company; an API login SHALL NOT change the active company. The SuperAdmin SHALL be able to tell how many users have multi-company access from the users with a stored default company.

#### Scenario: Regular user has no stored default
- **WHEN** a user is created in company A and has no extra companies
- **THEN** no default company is stored for the user
- **AND** the user's default company is A

#### Scenario: First extra company stores the default
- **WHEN** a user of company A gets access to company B, by a SuperAdmin grant or by creating B
- **THEN** A is stored as the user's default company

#### Scenario: Last extra company removed
- **WHEN** a user with default company A, active in B, loses access to B, their only extra company
- **THEN** the user's company is A
- **AND** no default company is stored for the user

#### Scenario: Counting multi-company users
- **WHEN** 2 of 10 users have extra companies
- **THEN** exactly those 2 users have a stored default company

#### Scenario: Login starts in the default company
- **WHEN** a user whose default company is A logs out while active in client company B and logs in again
- **THEN** the user's active company is A

#### Scenario: SuperAdmin changes the default company
- **WHEN** a SuperAdmin changes a user's default company to B
- **THEN** the user's default company is B
- **AND** the user can access B

#### Scenario: Non SuperAdmin cannot change the default company
- **WHEN** a CompanyAdmin tries to change any user's default company, including their own
- **THEN** the default company does not change

### Requirement: Client companies quota
The system SHALL store, per company, the maximum number of client companies it may create. A client company SHALL belong to exactly one parent company, the one it was created for, and SHALL NOT have client companies of its own. Used quota SHALL be the number of client companies of the parent, including deleted ones. Granting a user access to a company that has no parent company and no client companies SHALL make it a client company of the user's default company, unless that default company is a client company itself. The default quota SHALL be 0. Only a SuperAdmin SHALL change it. Lowering the quota SHALL NOT delete or detach existing client companies; it only prevents creating new ones until the company is under the limit.

#### Scenario: SuperAdmin sets the quota
- **WHEN** a SuperAdmin sets the quota of parent company F to 5
- **THEN** MultiCompanyAdmins of F can create up to 5 client companies for F

#### Scenario: Non SuperAdmin cannot change the quota
- **WHEN** a CompanyAdmin saves a company form including a quota value, for their own company or any other
- **THEN** the quota does not change

#### Scenario: Granting access to a company without parent
- **WHEN** a SuperAdmin grants a user of parent company F access to company C, which has no parent company and no client companies
- **THEN** C becomes a client company of F
- **AND** it counts against F's quota, even if the quota is exceeded

#### Scenario: Granting access to a client company of another parent company
- **WHEN** a user of parent company F is granted access to company D, a client company of parent company G
- **THEN** D stays a client company of G
- **AND** it does not count against F's quota

#### Scenario: Quota lowered below current usage
- **WHEN** parent company F has 3 client companies and a SuperAdmin lowers its quota to 1
- **THEN** the 3 client companies keep existing and remain accessible
- **AND** no new client company can be created for F

#### Scenario: Quota shared by the parent company
- **WHEN** two MultiCompanyAdmins of parent company F with quota 2 each create one client company
- **THEN** F has used its whole quota
- **AND** neither of them can create another client company for F

### Requirement: MultiCompanyAdmin role
The system SHALL provide a MultiCompanyAdmin role that administrates the user's company exactly like CompanyAdmin, inheriting every CompanyAdmin permission and behavior, and additionally allows accessing and creating client companies. A user with only the MultiCompanyAdmin role SHALL be able to do everything a CompanyAdmin can. Only a SuperAdmin SHALL assign or remove the MultiCompanyAdmin role. The SuperAdmin SHALL be able to tell how many users have the role.

#### Scenario: CompanyAdmin cannot assign the role
- **WHEN** a CompanyAdmin tries to assign the MultiCompanyAdmin role to any user, including themselves
- **THEN** the request is rejected
- **AND** the role is not offered in the CompanyAdmin's role list

#### Scenario: Editing their own data keeps their roles
- **WHEN** a MultiCompanyAdmin edits their own user, wherever they are working, and submits other roles
- **THEN** their data is updated and their roles do not change
- **AND** the role selector is not shown on their own user

#### Scenario: Roles the editor cannot assign are kept
- **WHEN** a CompanyAdmin edits a user who has a role the CompanyAdmin cannot assign
- **THEN** that role is kept

#### Scenario: SuperAdmin assigns the role
- **WHEN** a SuperAdmin assigns only the MultiCompanyAdmin role to a user
- **THEN** the user has every CompanyAdmin permission, sees all leads and customers of their company and can create client companies

### Requirement: Creating client companies within the quota
The parent company a MultiCompanyAdmin creates companies for SHALL be their default company, whatever company is active. A user with the MultiCompanyAdmin role SHALL be able to create a company when that parent company has available quota; the new company becomes a client company of the parent company. The creator SHALL automatically get access to the new company, keeping their roles. Creating a company SHALL be rejected when the parent company has no available quota. Users without the MultiCompanyAdmin or SuperAdmin role, including plain CompanyAdmins, SHALL NOT create companies. SuperAdmin company creation SHALL NOT be limited by any quota.

#### Scenario: MultiCompanyAdmin creates a client company
- **WHEN** a MultiCompanyAdmin whose default company is parent company F, with quota 2 and no client companies, creates company C
- **THEN** company C exists as a client company of F
- **AND** the MultiCompanyAdmin can access company C
- **AND** F's available quota is 1

#### Scenario: Creating while working in a client company
- **WHEN** a MultiCompanyAdmin whose default company is parent company F, active in client company C, creates company D
- **THEN** company D is a client company of F, not of C
- **AND** it counts against F's quota

#### Scenario: Quota reached
- **WHEN** a MultiCompanyAdmin whose parent company already has as many client companies as its quota tries to create a company
- **THEN** the request is rejected with a forbidden error
- **AND** no company is created

#### Scenario: Create option hidden without quota
- **WHEN** a MultiCompanyAdmin whose parent company has no available quota opens the company list
- **THEN** the option to create a company is not shown

#### Scenario: Regular user cannot create companies
- **WHEN** a user with the Seller role tries to create a company
- **THEN** the request is rejected with a forbidden error

#### Scenario: Plain CompanyAdmin cannot create companies
- **WHEN** a CompanyAdmin without the MultiCompanyAdmin role, whose company has quota, tries to create a company
- **THEN** the request is rejected with a forbidden error

### Requirement: Switching the active company
A user SHALL work in one active company at a time, and all company data the user sees or changes SHALL belong to the active company. A user with access to more than one company SHALL be able to switch the active company from the navigation bar. Switching SHALL only be allowed to companies the user can access and that are not deleted. Every switch SHALL be written to the security log with the previous and the new company.

#### Scenario: Switch to an accessible company
- **WHEN** a user with access to companies A and B, active in A, switches to B
- **THEN** the active company is B
- **AND** lists such as leads, customers and orders show only company B data
- **AND** the security log records the switch from A to B

#### Scenario: Switch to a company without access
- **WHEN** a user switches to a company they cannot access
- **THEN** the request is rejected with a forbidden error
- **AND** the active company does not change
- **AND** the attempt is written to the security log

#### Scenario: Switch to a deleted company
- **WHEN** a user switches to a company that was deleted
- **THEN** the request is rejected
- **AND** the active company does not change

#### Scenario: Switcher hidden for single company users
- **WHEN** a user who can access only one company opens any page
- **THEN** the company switcher is not shown

#### Scenario: Switcher lists accessible companies
- **WHEN** a user who can access companies A and B opens any page
- **THEN** the company switcher lists A and B and marks the active one

### Requirement: Revoking the active company
When a user's access to their active company is revoked, the system SHALL make the user's default company their active company.

#### Scenario: Active company revoked
- **WHEN** a user whose default company is A, active in company B, loses access to company B
- **THEN** the user's active company becomes A
- **AND** the user can no longer switch to company B

### Requirement: No overhead for single company users
Users who can only access their default company SHALL NOT run additional database queries per request because of this capability.

#### Scenario: Regular user opens the dashboard
- **WHEN** a user with no extra companies opens the dashboard
- **THEN** the number of database queries is the same as before multi-company access existed

### Requirement: Company list scoped to accessible companies
The SuperAdmin, CompanyAdmin and MultiCompanyAdmin roles SHALL be able to open the company list; any other role SHALL be rejected. CompanyAdmin and MultiCompanyAdmin SHALL only see the companies they can access, only active ones, and only edit those companies; editing any other company SHALL be rejected. Only the SuperAdmin SHALL see every company, filter deleted companies and see the companies quota of every company.

#### Scenario: CompanyAdmin lists companies
- **WHEN** a CompanyAdmin with access to companies A and B opens the company list
- **THEN** the list shows A and B only, with their companies quota

#### Scenario: Single company CompanyAdmin lists companies
- **WHEN** a CompanyAdmin without extra companies opens the company list
- **THEN** the list shows only their company

#### Scenario: Other roles cannot open the list
- **WHEN** a user with the Seller or Support role opens the company list
- **THEN** the request is rejected with a forbidden error

#### Scenario: Editing an accessible company
- **WHEN** a CompanyAdmin active in company A edits company B they can access
- **THEN** company B is updated without switching to it

#### Scenario: Editing a company without access
- **WHEN** a CompanyAdmin opens or saves the edit form of a company they cannot access
- **THEN** the request is rejected with a forbidden error

#### Scenario: SuperAdmin lists companies
- **WHEN** a SuperAdmin opens the company list
- **THEN** the list shows every company

### Requirement: Actions are attributed to the real user
Working in another company SHALL NOT change the identity of the user. Records the user creates or updates in any accessible company SHALL be attributed to that user.

#### Scenario: Lead created in a client company
- **WHEN** a multi-company user active in client company B creates a lead
- **THEN** the lead belongs to company B
- **AND** the lead is recorded with that user's own account (as seller when no other seller is chosen)
