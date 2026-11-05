# Spec Delta

## Purpose

Lets a CRM user save the LinkedIn profile they are viewing as a contact of a lead or customer with a few clicks, reviewing and fixing the captured data before it reaches the CRM, from Chrome, Edge or Firefox.

## ADDED Requirements

### Requirement: Supported browsers
The extension SHALL install and work from the same source in current versions of Chrome, Edge and Firefox, without a build step or any tooling such as Node.

#### Scenario: Same package in every browser
- **WHEN** the extension folder is loaded unpacked in Chrome, Edge or Firefox
- **THEN** login, capture and saving a contact work the same in all three

### Requirement: Login to the CRM
The extension SHALL let the user log in with the CRM URL, email and password. On success it SHALL keep the API token in the browser's local extension storage and SHALL NOT store the password. Wrong credentials SHALL show an error and keep the user logged out.

#### Scenario: Successful login
- **WHEN** the user enters `https://crm.example.com`, a valid email and password and logs in
- **THEN** the extension shows the user's name and company
- **AND** the password is not kept anywhere

#### Scenario: Wrong credentials
- **WHEN** the user logs in with a wrong password
- **THEN** the extension shows an invalid credentials error and stays logged out

#### Scenario: Unreachable CRM URL
- **WHEN** the CRM URL does not answer or is not a CRM
- **THEN** the extension shows that it cannot connect to that URL

### Requirement: CRM origin permission
The extension SHALL only send requests to the CRM URL the user entered, and SHALL ask the browser for permission to access that origin at login. Denying the permission SHALL keep the user logged out.

#### Scenario: Permission denied
- **WHEN** the user denies access to the CRM origin at login
- **THEN** the extension does not log in and explains that access is needed

### Requirement: Session lifetime
The extension SHALL refresh the token before it expires while the session can still be refreshed. When the session can no longer be refreshed, or the user is inactive in the CRM, the extension SHALL return to the login form. Logout SHALL revoke the token in the CRM and delete it from the extension.

#### Scenario: Expired token is refreshed
- **GIVEN** the token expired but can still be refreshed
- **WHEN** the user opens the form on a profile
- **THEN** the extension refreshes the token and the form works without a new login

#### Scenario: Session can no longer be refreshed
- **WHEN** the CRM rejects both the token and its refresh
- **THEN** the extension shows the login form

#### Scenario: Logout
- **WHEN** the user logs out
- **THEN** the token is revoked in the CRM and removed from the extension

### Requirement: Capture only the profile being viewed
On a LinkedIn profile page (`linkedin.com/in/...`) the extension SHALL offer an "Add to CRM" action. It SHALL read only the profile shown in the open page when the user asks for it, and SHALL NOT open pages, navigate, scroll or read profiles on its own. It SHALL NOT capture, store or show the profile photo or any other image.

#### Scenario: No photo
- **WHEN** a profile with a photo is captured and saved
- **THEN** neither the form nor the request to the CRM includes the photo or its URL

#### Scenario: Action on a profile
- **WHEN** a logged in user opens a LinkedIn profile
- **THEN** the "Add to CRM" action is available

#### Scenario: Not a profile page
- **WHEN** the user is on the LinkedIn feed, a search or a company page
- **THEN** the "Add to CRM" action is not offered

#### Scenario: Not logged in
- **WHEN** a user who is not logged in uses "Add to CRM"
- **THEN** the extension asks them to log in first

### Requirement: Editable contact form
The extension SHALL show a form prefilled with the captured first name, last name, job title, country, province, city and profile URL. The captured company is not a contact field: it starts the lead or customer search and names a new lead, so the contact's company is always the chosen lead or customer. Every field SHALL be editable, first name SHALL be required, and fields the page did not provide SHALL be left empty instead of failing. Email and phone SHALL be optional fields.

#### Scenario: Prefilled form
- **GIVEN** the profile shows "Jane Doe", "Head of Sales at Acme" and "Madrid, Community of Madrid, Spain"
- **WHEN** the user uses "Add to CRM"
- **THEN** the form shows first name "Jane", last name "Doe", job title "Head of Sales", country Spain, province "Community of Madrid" and city "Madrid"
- **AND** the lead or customer search starts with "Acme"

#### Scenario: Missing data
- **GIVEN** the profile shows no location
- **WHEN** the user uses "Add to CRM"
- **THEN** the location fields are empty and the form still opens

#### Scenario: Fix before saving
- **WHEN** the user changes the job title in the form and saves
- **THEN** the contact is saved with the edited job title

#### Scenario: Name required
- **WHEN** the user empties the first name and saves
- **THEN** the contact is not saved and the first name field shows an error

### Requirement: Choose the lead or customer
The form SHALL let the user search and pick one lead or customer of their company as the owner of the contact, including leads assigned to other sellers. The search SHALL start with the captured company name.

#### Scenario: Suggested by company
- **GIVEN** the company has a lead "Acme Robotics"
- **WHEN** the form opens for a profile working at "Acme Robotics"
- **THEN** "Acme Robotics" is offered as the owner

#### Scenario: Pick a customer
- **WHEN** the user searches "Initech" and picks the customer "Initech"
- **THEN** the contact is saved as a contact of that customer

### Requirement: Create the lead from the profile
When no lead or customer fits, the user SHALL be able to create a new lead from the form, named after the captured company and assigned to the user as seller. The country of the lead SHALL be the one in the form or, when empty, the country of the user's company. The new lead SHALL then be the owner of the contact.

#### Scenario: New lead
- **GIVEN** no lead or customer matches "Hooli"
- **WHEN** the user chooses to create the lead "Hooli" and saves
- **THEN** a lead "Hooli" assigned to the user exists and the contact belongs to it

### Requirement: Warn about duplicated contacts
When the form opens, the extension SHALL check whether a contact of the company already has the profile URL. If one exists, it SHALL warn the user, show its lead or customer and offer to open it in the CRM. The user MAY still save a new contact.

#### Scenario: Already in the CRM
- **GIVEN** the profile URL belongs to a contact of the lead "Acme"
- **WHEN** the form opens
- **THEN** the extension warns that the contact already exists in "Acme" and offers to open it

#### Scenario: New person
- **WHEN** no contact has the profile URL
- **THEN** no warning is shown

### Requirement: Save the contact
Saving SHALL create the contact in the CRM with the form data and the profile URL, and SHALL show a confirmation with a link to the lead or customer in the CRM. Validation errors returned by the CRM SHALL be shown on their fields without losing what the user typed.

#### Scenario: Saved
- **WHEN** the user saves a valid form
- **THEN** the contact exists in the CRM with the LinkedIn URL and the extension shows a link to its lead or customer

#### Scenario: CRM rejects a field
- **WHEN** the CRM rejects the job title as too long
- **THEN** the job title field shows the error and the other fields keep their values

### Requirement: Data stays between LinkedIn and the CRM
The extension SHALL send captured profile data only to the user's CRM and SHALL NOT send it to any other service.

#### Scenario: No third parties
- **WHEN** a contact is captured and saved
- **THEN** the only requests carrying its data go to the user's CRM URL

### Requirement: Interface languages
The extension interface SHALL be available in English and Spanish, following the browser language, with English as default.

#### Scenario: Spanish browser
- **WHEN** the browser language is Spanish
- **THEN** the extension interface is shown in Spanish
