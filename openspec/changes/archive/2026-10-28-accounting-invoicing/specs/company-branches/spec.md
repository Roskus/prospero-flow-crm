# Spec Delta

## MODIFIED Requirements

### Requirement: Branch management
Users with permission to read the company SHALL be able to list the branches of their active company. Users with permission to update the company SHALL be able to create, edit, delete and set as main its branches. Deleting a branch SHALL keep it for the records that reference it (soft delete), SHALL hide it from branch selects, and users whose default branch it was SHALL fall back to the main branch. When creating a branch the system SHALL suggest the next free numeric code of the company (`0002`, `0003`...), which the user can change. The code of a branch that has issued invoices SHALL NOT be changed, because it is the series of its invoices.

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

#### Scenario: Code of a branch with invoices
- **WHEN** a user changes the code of a branch that has issued invoices
- **THEN** the branch is not saved and a validation error on the code says it is the invoice series
- **AND** the code field is read only in the branch form
