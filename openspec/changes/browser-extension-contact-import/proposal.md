# Proposal

## Why

Sellers find most of their contacts on LinkedIn and copy them into the CRM by hand: name, job title, company and location, one field at a time. A browser extension that reads the profile the seller is viewing and saves it as a contact of a lead or customer removes that copy work and keeps the LinkedIn URL in the CRM, so the same person is not added twice. The API is now ready for it: contacts no longer require email or phone, the API keeps every contact field, contacts are validated against the user's company and lists are paginated.

## What Changes

- New browser extension in `browser-extension/` (Manifest V3, vanilla JavaScript as simple as possible: no Node, npm, build step or dependencies) that works in Chrome, Edge and Firefox.
- **Login**: the user enters the CRM URL, email and password; the extension gets a JWT from `POST /api/auth/login`, refreshes it with `POST /api/auth/refresh` and never stores the password. Logout revokes the token.
- **Capture**: on a LinkedIn profile (`linkedin.com/in/*`) the extension reads the profile the user is viewing (first name, last name, job title, company, location and profile URL) and shows an editable form prefilled with that data. The user fixes any field by hand before saving. Contact photos are never captured, stored or shown.
- **Target**: the user picks an existing lead or customer of their company by searching, or creates a new lead from the profile's company. Every seller sees every lead of the company (business rule).
- **Duplicates**: before saving, the extension warns when a contact with the same LinkedIn URL already exists in the company and links to it.
- **Save**: the contact is created with `POST /api/contact` (lead or customer, name, job title, LinkedIn URL, location; email and phone optional).
- **API search**: `GET /api/lead` and `GET /api/customer` accept `search` (name and business name), keeping their paginated response; `GET /api/contact` accepts `linkedin` to find a contact by profile URL.
- The extension only reads the page the user opened. No bulk import, crawling or automated navigation on LinkedIn.

## Capabilities

### New Capabilities
- `browser-extension`: the CRM browser extension — login and session, reading the LinkedIn profile being viewed, the editable contact form, choosing or creating the lead or customer, the duplicate warning and saving the contact.
- `api-record-search`: searching leads and customers by name and finding contacts by LinkedIn URL through the API, scoped to the user's company.

### Modified Capabilities

## Impact

- **New folder**: `browser-extension/` (manifest, background script with the CRM client, content script for LinkedIn, panel and popup pages, a browser test page). Loaded unpacked; no tooling needed to develop or run it.
- **API**: `LeadListController` and `CustomerListController` gain an optional `search` query; `ContactListController` gains an optional `linkedin` query. Responses keep their current shape. OpenAPI docs and tests updated.
- **Login**: `expires_in` reports the configured token lifetime (`JWT_TTL`) in seconds; today it reports twice the real value.
- **Reused API**: `POST /api/auth/login`, `refresh`, `me`, `logout`; `POST /api/contact`; `POST /api/lead` (name, seller and country) for the new lead.
- **Security**: JWT in extension local storage only; the CRM origin is granted at runtime as an optional host permission; the extension never sends LinkedIn data anywhere but the user's CRM.
- **LinkedIn**: reading the DOM of the open profile is fragile because LinkedIn changes its markup; the parser tolerates missing fields and the form lets the user complete them. Respects LinkedIn terms by acting only on the profile the user opened.
- **Translations**: extension UI in English and Spanish (`_locales/`), following the CRM convention of adding both at once.
