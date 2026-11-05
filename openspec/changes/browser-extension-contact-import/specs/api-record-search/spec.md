# Spec Delta

## Purpose

Lets API clients, such as the CRM browser extension, find the lead, customer or contact they need without downloading every record of the company: leads and customers by name, contacts by LinkedIn profile URL, always within the user's company.

## ADDED Requirements

### Requirement: Search leads by name
`GET /api/lead` SHALL accept an optional `search` query. When present, the list SHALL only include leads of the user's company whose name or business name contains the searched text, case insensitive. The response SHALL keep the paginated list format (`count`, `leads` and the pagination fields), with `count` being the number of matching leads.

#### Scenario: Matching by name
- **GIVEN** the company has the leads "Acme Robotics" and "Globex"
- **WHEN** the user requests `GET /api/lead?search=acme`
- **THEN** the response lists only "Acme Robotics"
- **AND** `count` is 1

#### Scenario: Matching by business name
- **GIVEN** the company has a lead named "Acme" with business name "Acme Industrial S.L."
- **WHEN** the user requests `GET /api/lead?search=industrial`
- **THEN** the response lists that lead

#### Scenario: No search keeps the full list
- **WHEN** the user requests `GET /api/lead` without `search`
- **THEN** the response lists every lead of the company, paginated

#### Scenario: Pagination links keep the search
- **GIVEN** more leads match than fit in one page
- **WHEN** the user requests `GET /api/lead?search=acme&per_page=2`
- **THEN** `next_page_url` keeps `search=acme` and `per_page=2`

### Requirement: Lead search covers every lead of the company
The lead search SHALL include every lead of the user's company whoever its assigned seller is, and SHALL NOT include leads of other companies.

#### Scenario: Lead of another seller
- **GIVEN** a lead "Acme" of the company is assigned to another seller
- **WHEN** a seller requests `GET /api/lead?search=acme`
- **THEN** the response lists "Acme"

#### Scenario: Lead of another company
- **GIVEN** another company has a lead "Acme"
- **WHEN** the user requests `GET /api/lead?search=acme`
- **THEN** that lead is not listed

### Requirement: Search customers by name
`GET /api/customer` SHALL accept an optional `search` query. When present, the list SHALL only include customers of the user's company whose name or business name contains the searched text, case insensitive, keeping the paginated list format (`count`, `customers` and the pagination fields).

#### Scenario: Matching customer
- **GIVEN** the company has the customers "Initech" and "Umbrella"
- **WHEN** the user requests `GET /api/customer?search=init`
- **THEN** the response lists only "Initech"

#### Scenario: Customer of another company
- **GIVEN** another company has a customer "Initech"
- **WHEN** the user requests `GET /api/customer?search=init`
- **THEN** that customer is not listed

### Requirement: Find contacts by LinkedIn URL
`GET /api/contact` SHALL accept an optional `linkedin` query with a LinkedIn profile URL. When present, the list SHALL only include contacts of the user's company with that profile URL. URLs SHALL match regardless of scheme, `www.`, trailing slash, query string and letter case, so `https://www.linkedin.com/in/jane-doe/` and `linkedin.com/in/Jane-Doe` find the same contact.

#### Scenario: Existing contact
- **GIVEN** a contact of the company has the LinkedIn URL `https://www.linkedin.com/in/jane-doe`
- **WHEN** the user requests `GET /api/contact?linkedin=https://linkedin.com/in/jane-doe/`
- **THEN** the response lists that contact with its lead or customer

#### Scenario: No contact with that profile
- **WHEN** the user requests `GET /api/contact?linkedin=https://www.linkedin.com/in/nobody`
- **THEN** the response lists no contacts and `count` is 0

#### Scenario: Contact of another company
- **GIVEN** a contact of another company has that LinkedIn URL
- **WHEN** the user requests `GET /api/contact?linkedin=...` with that URL
- **THEN** that contact is not listed

### Requirement: Country list
`GET /api/country` SHALL list the countries the CRM accepts as `country_id`, from its countries library, with their name in the requested language (`lang`, English or Spanish) or in the application language by default, sorted by name, as `count` and `countries` with `id` and `name`. An unsupported language SHALL return a validation error.

#### Scenario: Countries in Spanish
- **WHEN** the user requests `GET /api/country?lang=es`
- **THEN** the response lists Spain as `{"id": "es", "name": "España"}`

#### Scenario: Unsupported language
- **WHEN** the user requests `GET /api/country?lang=xx`
- **THEN** the response is a validation error on `lang`

### Requirement: Search parameters are documented
The OpenAPI document SHALL describe the `search` query of the lead and customer lists and the `linkedin` query of the contact list as optional string parameters.

#### Scenario: Documented parameters
- **WHEN** a client reads the OpenAPI document
- **THEN** `GET /lead` and `GET /customer` list a `search` query parameter and `GET /contact` lists a `linkedin` query parameter
