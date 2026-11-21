# Spec Delta

## Purpose

Tracks who holds each company asset at any moment and keeps the full history of past holders, so responsibility over vehicles and devices is always traceable.

## ADDED Requirements

### Requirement: Assign asset to a user
A user with `update asset` permission SHALL be able to assign an `available` asset to an active user of the same company, with a start date (default today) and optional notes. The asset status SHALL become `assigned`. The same SHALL be possible from the asset form by choosing status `assigned` and a holder.

#### Scenario: Assign available asset
- **WHEN** a user assigns available notebook NB-001 to Ana starting today
- **THEN** NB-001 shows Ana as current holder and status `assigned`

#### Scenario: User of another company
- **WHEN** a user tries to assign an asset to a user of another company
- **THEN** the assignment is rejected with a validation error on the user field

### Requirement: Only one open assignment per asset
An asset SHALL have at most one open assignment. Assets with status `assigned`, `in_maintenance` or `retired` SHALL NOT be assigned until they are available again.

#### Scenario: Assign already assigned asset
- **WHEN** a user tries to assign an asset currently assigned to Ana to Luis
- **THEN** the assignment is rejected with a message that the asset must be returned first

#### Scenario: Assign retired asset
- **WHEN** a user tries to assign a retired asset
- **THEN** the assignment is rejected

### Requirement: Return asset
A user with `update asset` permission SHALL be able to return an assigned asset, providing an end date (default today, not earlier than the start date) and optional notes. The asset status SHALL become `available`.

#### Scenario: Return asset
- **WHEN** a user returns NB-001 from Ana today
- **THEN** the assignment is closed with today as end date and NB-001 becomes `available`

#### Scenario: End date before start date
- **WHEN** a user returns an asset with an end date earlier than the assignment start date
- **THEN** the return is rejected with a validation error on the end date

### Requirement: Assignment history
The asset detail SHALL show every assignment of the asset, newest first, with user, start date, end date and notes.

#### Scenario: View history
- **WHEN** a user opens the detail of an asset assigned first to Ana and later to Luis
- **THEN** both assignments are listed, Luis first

### Requirement: Correct an assignment
A user with `update asset` permission SHALL be able to correct a recorded assignment: holder (an active user of the company, or the recorded holder even if deactivated), start date, return date and notes. An open assignment SHALL stay open (it is closed by returning the asset); a closed one SHALL keep a return date not earlier than its start. The corrected period SHALL NOT overlap another assignment of the same asset, although one may end the day the next starts.

#### Scenario: Fix a wrong start date
- **WHEN** a user changes the start date of Ana's open assignment from `2026-11-21` to `2026-11-02`
- **THEN** the history shows Ana from `02/11/2026` and the assignment is still open

#### Scenario: Overlapping correction
- **WHEN** a user moves the start date of Luis's assignment before the return date of Ana's previous assignment of the same asset
- **THEN** the save is rejected with a validation error on the start date

### Requirement: Assets held by a user
The system SHALL list the assets currently assigned to a given user, visible from the inventory filters.

#### Scenario: Assets of a user
- **WHEN** a user filters the inventory by assigned user Ana
- **THEN** every asset with an open assignment to Ana is shown

### Requirement: Deleted holder keeps history
Deleting a user SHALL NOT delete assignment history; the history SHALL still display the user's name.

#### Scenario: Holder deleted
- **WHEN** a user who had an asset assigned in the past is deleted
- **THEN** the asset history still shows that assignment with the user's name
