# company-branches Specification

## Purpose
Lets a company operate from several locations that share its legal identity: each branch has its own address and contact data, one of them is the main branch, users work from a default branch and every order records the branch it belongs to.

## Requirements

### Requirement: Branches of a company
A company SHALL have one or more branches. A branch SHALL belong to exactly one company and SHALL have a name and a code, and MAY have an address (the same address fields, validation, form and display as companies), phone and email. The code SHALL be unique among the branches of the same company. Branches SHALL NOT be shared between companies, and users SHALL only see and use branches of their active company.

#### Scenario: Branch belongs to the active company
- **WHEN** a user active in company A creates a branch
- **THEN** the branch belongs to company A

#### Scenario: Branches of another company are hidden
- **WHEN** a user active in company A lists branches or opens a branch of company B by its id
- **THEN** branches of company B are not listed
- **AND** opening the branch of company B returns not found

#### Scenario: Duplicated code in the same company
- **WHEN** a user saves a branch with a code already used by another branch of the same company
- **THEN** the branch is not saved and a validation error is shown on the code

#### Scenario: Same code in different companies
- **WHEN** company A and company B each have a branch with code `0001`
- **THEN** both branches are valid

### Requirement: Main branch
Every company SHALL have exactly one main branch at all times. When a company is created, by any means, the system SHALL create its main branch with code `0001`, named after the company and with a copy of the company address, phone and email. Changing the company address later SHALL NOT change any branch. A user who can update the company SHALL be able to make another branch the main branch; the previous main branch SHALL stop being main. The main branch SHALL NOT be deleted.

#### Scenario: New company gets a main branch
- **WHEN** a company is created with an address in Buenos Aires
- **THEN** the company has one branch, marked as main, with code `0001` and the same address

#### Scenario: Existing companies get a main branch
- **WHEN** the change is deployed on a database with existing companies
- **THEN** every existing company, including soft deleted ones, has exactly one main branch built from its data

#### Scenario: Changing the main branch
- **WHEN** a user makes branch "Centro" the main branch of a company whose main branch is "Casa matriz"
- **THEN** "Centro" is the main branch
- **AND** "Casa matriz" is no longer the main branch

#### Scenario: Main branch cannot be deleted
- **WHEN** a user tries to delete the main branch
- **THEN** the branch is not deleted and an error message is shown

#### Scenario: Deleted branch cannot become main
- **WHEN** a user makes a branch the main branch after another user deleted it
- **THEN** the main branch does not change and an error message is shown

### Requirement: Branch management
Users with permission to read the company SHALL be able to list the branches of their active company. Users with permission to update the company SHALL be able to create, edit, delete and set as main its branches. Deleting a branch SHALL keep it for the records that reference it (soft delete), SHALL hide it from branch selects, and users whose default branch it was SHALL fall back to the main branch. When creating a branch the system SHALL suggest the next free numeric code of the company (`0002`, `0003`...), which the user can change.

#### Scenario: User without update permission
- **WHEN** a user with only read company permission tries to create, edit or delete a branch
- **THEN** the request is rejected with a forbidden error

#### Scenario: Suggested code
- **WHEN** a user opens the new branch form in a company with branches `0001` and `0002`
- **THEN** the code field is prefilled with `0003`

#### Scenario: Deleted branch keeps its orders
- **WHEN** a user deletes a branch that has orders
- **THEN** the branch is no longer offered in branch selects
- **AND** its orders still show the branch name

#### Scenario: Deleting a user's default branch
- **WHEN** a branch that is the default branch of a user is deleted
- **THEN** the user's current branch is the main branch of the company

#### Scenario: Branches in settings
- **WHEN** a user with read company permission opens the settings screen
- **THEN** a Branches entry is shown in the Organization group

### Requirement: User default branch
Every user SHALL have a default branch, which SHALL be a branch of the user's default company. When a user is created, their default branch SHALL be the main branch of their company unless another branch of that company is chosen. Users that existed before branches SHALL get the main branch of their company. Changing the main branch later SHALL NOT change the default branch of existing users. A user's current branch SHALL be their default branch when it belongs to the active company and is not deleted; otherwise it SHALL be the main branch of the active company. Users who can manage users SHALL be able to change a user's default branch in the user form.

#### Scenario: New user gets the main branch
- **WHEN** a user is created in company A without choosing a branch
- **THEN** the user's default branch is the main branch of A

#### Scenario: Existing users
- **WHEN** the change is deployed on a database with existing users
- **THEN** every existing user's default branch is the main branch of their default company

#### Scenario: User with another default branch
- **WHEN** a user whose default branch is "Norte" of company A works in company A
- **THEN** their current branch is "Norte"

#### Scenario: User created before the main branch
- **WHEN** a user of company A is created before A has a main branch
- **THEN** the user's default branch is stored as the main branch of A once it is created

#### Scenario: Main branch changed
- **WHEN** a company makes "Centro" its main branch and user U has "Casa matriz" as default branch
- **THEN** U's default branch is still "Casa matriz"

#### Scenario: Multi-company user in a client company
- **WHEN** a user whose default branch is "Norte" of company A switches to client company B
- **THEN** their current branch is the main branch of B
- **AND** their default branch is still "Norte"

#### Scenario: Branch of another company rejected
- **WHEN** a user form is saved with a default branch that belongs to another company
- **THEN** the user is not saved and a validation error is shown on the branch

### Requirement: Order branch
Every order SHALL belong to a branch of its company. A new order SHALL take the current branch of the user creating it unless another branch of the same company is chosen. The order form SHALL show a branch select only when the company has more than one active branch. The order list SHALL show the branch and allow filtering by it when the company has more than one branch. Orders that existed before branches SHALL belong to their company's main branch. The API SHALL accept an optional branch on order create and update, validated against the active company, and SHALL return the branch of an order.

#### Scenario: Order created in the current branch
- **WHEN** a user whose current branch is "Norte" creates an order without choosing a branch
- **THEN** the order belongs to "Norte"

#### Scenario: Choosing another branch
- **WHEN** a user creates an order choosing branch "Centro" of the same company
- **THEN** the order belongs to "Centro"

#### Scenario: Branch of another company rejected
- **WHEN** an order is saved, from the web or the API, with a branch of another company
- **THEN** the order is not saved and a validation error is shown on the branch

#### Scenario: Single branch company
- **WHEN** a company has only its main branch
- **THEN** the order form does not show a branch select
- **AND** the order list does not show the branch column or filter

#### Scenario: Filtering orders by branch
- **WHEN** a user of a company with branches "Casa matriz" and "Norte" filters the order list by "Norte"
- **THEN** only orders of "Norte" are listed

#### Scenario: Existing orders
- **WHEN** the change is deployed on a database with existing orders
- **THEN** every existing order belongs to the main branch of its company
