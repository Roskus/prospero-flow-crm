# Spec Delta

## Purpose

Gives every branch a cash account where cash payments are recorded, so the money received at each location has a balance like any bank account.

## ADDED Requirements

### Requirement: Cash account type
Accounts SHALL support the type cash. A cash account SHALL have a name and a currency and SHALL NOT require bank, IBAN, account number or other bank fields; the account form SHALL hide those fields for the cash type. A cash account SHALL show its balance like any other account.

#### Scenario: Creating a cash account by hand
- **WHEN** a user creates an account of type cash with only a name and a currency
- **THEN** the account is saved

### Requirement: Cash account per branch
Every branch SHALL have a cash account in the company currency, created automatically when the branch is created, named "Cash" followed by the branch name. When the change is deployed, every existing branch SHALL get its cash account. The cash account of a branch SHALL NOT be deleted while the branch exists.

#### Scenario: New branch gets a cash account
- **WHEN** a user creates branch "Centro" in a company whose currency is ARS
- **THEN** the company has a cash account "Cash Centro" in ARS linked to that branch

#### Scenario: Existing branches get a cash account
- **WHEN** the change is deployed on a database with branches
- **THEN** every branch has exactly one cash account

#### Scenario: Deleting the cash account of a branch
- **WHEN** a user tries to delete the cash account of an existing branch
- **THEN** the account is not deleted and an error message is shown

### Requirement: Accounts shown on invoices
Accounts SHALL have an option to be shown on invoices, off by default, editable from the account form. Only accounts with this option on SHALL appear in the bank transfer details of invoice PDFs. Cash accounts SHALL NOT offer this option.

#### Scenario: Marking an account
- **WHEN** a user turns on "Show on invoices" for a bank account
- **THEN** new and existing invoice PDFs list that account in the bank transfer details
