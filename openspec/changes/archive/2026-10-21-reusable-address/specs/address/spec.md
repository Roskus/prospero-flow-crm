# Spec Delta

## Purpose

Defines one address for every entity that has a location (leads, customers, companies, suppliers and contacts, and later branches): the same fields, validation, form and display everywhere.

## ADDED Requirements

### Requirement: Address fields
Leads, customers, companies and suppliers SHALL store the same address fields: country, province, city, locality, street, extra address (floor, office, apartment), zipcode, latitude and longitude. All fields SHALL be optional except where an entity already requires the country (leads and customers). Existing address data SHALL be kept unchanged.

#### Scenario: Company with full address
- **WHEN** a user saves a company with locality "Palermo", extra address "Piso 3 B", latitude and longitude
- **THEN** the company keeps all those values when the form is opened again

#### Scenario: Supplier with city
- **WHEN** a user saves a supplier with city "Rosario"
- **THEN** the supplier form shows "Rosario" in the city field when opened again

#### Scenario: Existing data kept
- **WHEN** the change is deployed on a database with leads, customers, companies and suppliers with addresses
- **THEN** every existing address field keeps its value

### Requirement: Address validation
Address fields SHALL be validated with the same rules in every web form, API endpoint and import: country as a valid two letter country code, province and locality up to 80 characters, city up to 50, street and extra address up to 80, zipcode up to 10, latitude between -90 and 90 and longitude between -180 and 180, each coordinate required when the other is present. A value over the limit SHALL produce a validation error on that field and SHALL NOT produce a server error.

#### Scenario: Street too long
- **WHEN** a user saves a company, supplier, lead or customer with a street of 81 characters
- **THEN** the record is not saved and a validation error is shown on the street

#### Scenario: Only one coordinate
- **WHEN** a user saves an address with latitude but without longitude
- **THEN** the record is not saved and a validation error is shown on the longitude

#### Scenario: Supplier API with a long province
- **WHEN** a supplier is created through the API with a province of 81 characters
- **THEN** the API answers with a validation error on the province

### Requirement: Saving the address
Saving a lead, customer, company or supplier SHALL store every address field sent in the form, the API or the import. Empty fields SHALL be stored as empty (null), not as empty strings.

#### Scenario: Lead extra address saved
- **WHEN** a user saves a lead or customer with extra address "Oficina 12"
- **THEN** the extra address is "Oficina 12" when the record is opened again

#### Scenario: Clearing a field
- **WHEN** a user clears the locality of a customer and saves
- **THEN** the customer has no locality

### Requirement: Address form
Lead, customer, company and supplier forms SHALL show the same address section: an address search, country (searchable select), province, city, locality, street, extra address, zipcode and a map. Searching an address SHALL place it on the map and fill the coordinates. The extra address field SHALL be labeled "Address extra" ("Dirección adicional") with the placeholder "Floor, office, suite..." ("Piso, oficina, depto..."), translated in English and Spanish.

#### Scenario: Same fields everywhere
- **WHEN** a user opens the lead, customer, company or supplier form
- **THEN** each shows the search, country, province, city, locality, street, extra address, zipcode and map

#### Scenario: Spanish labels
- **WHEN** a user with Spanish language opens any of those forms
- **THEN** the extra address field shows "Dirección adicional" with placeholder "Piso, oficina, depto..."

#### Scenario: Existing values in the form
- **WHEN** a user opens the form of a supplier with country Spain and zipcode "28001"
- **THEN** Spain is selected and the zipcode shows "28001"

### Requirement: Address display
Wherever an address is shown, the system SHALL show the full address built from its non empty fields in this order: street and extra address, locality, zipcode and city, province, country name. Empty parts SHALL be skipped without leaving extra separators. An address with no fields SHALL show nothing. The lead/customer detail, the order view and the order PDF (company and customer) SHALL use this format.

#### Scenario: Full address
- **WHEN** a customer has street "Córdoba 1234", extra "Piso 3 B", zipcode "2000", city "Rosario", province "Santa Fe" and country Argentina
- **THEN** the address is shown as "Córdoba 1234, Piso 3 B, 2000 Rosario, Santa Fe, Argentina"

#### Scenario: Partial address
- **WHEN** a company has only city "Madrid" and country Spain
- **THEN** the address is shown as "Madrid, Spain"

#### Scenario: Order PDF
- **WHEN** a user downloads the PDF of an order whose customer has street, zipcode and city
- **THEN** the PDF shows the customer street, zipcode and city, not only the street

### Requirement: Contact address
Contacts of leads, customers and suppliers SHALL have an optional address with the same fields, validation and form (including search and map) as the other entities; no address field SHALL be required. The contact lists of leads, customers and suppliers SHALL show the country name and the city of each contact, and SHALL leave them empty when the contact has none.

#### Scenario: Contact without address
- **WHEN** a user saves a contact without filling any address field
- **THEN** the contact is saved without address

#### Scenario: Contact with address
- **WHEN** a user saves a customer contact with country Spain, city "Madrid" and street "Calle Mayor 1"
- **THEN** the contact form shows those values when opened again

#### Scenario: Contact list shows country and city
- **WHEN** a customer has a contact in Madrid, Spain and another contact without address
- **THEN** the contact list shows "Spain" and "Madrid" for the first contact
- **AND** shows empty country and city for the second one

#### Scenario: Supplier contact address
- **WHEN** a user saves a supplier contact with city "Rosario"
- **THEN** the supplier contact list shows "Rosario"

