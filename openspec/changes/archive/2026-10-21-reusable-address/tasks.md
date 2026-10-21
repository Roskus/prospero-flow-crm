# Tasks

## 1. Address core

- [x] 1.1 Create `App\ValueObjects\Address` (readonly fields, `fromArray()`, `toArray()`, `isEmpty()`, `hasCoordinates()`, `lines()`, `oneLine()` with cached country name); verify with unit tests for full, partial and empty addresses, empty strings turned into null, lowercase country and the "Córdoba 1234, Piso 3 B, 2000 Rosario, Santa Fe, Argentina" format
- [x] 1.2 Make `Address` `Castable` and create `App\Traits\HasAddress` adding the `address` cast; verify with a model test that reading builds the object from columns and assigning writes every column, including clearing one field
- [x] 1.3 Create `App\Rules\AddressRules::rules(bool $countryRequired)` with the shared lengths and coordinate rules; verify with a unit test of the rules array and validation of 81 character street and a lone latitude
- [x] 1.4 Register the `address()` `Blueprint` macro in `AppServiceProvider`; verify with a test creating a temporary table with the macro and checking its columns

## 2. Database

- [x] 2.1 Create the migration adding `locality`, `address_extra` (varchar 80), `latitude` decimal(10,8), `longitude` decimal(11,8) to `company` and `supplier` and changing `province` to varchar(80) keeping it nullable; verify with `migrate`, `migrate:rollback`, the `database-schema` tool and that existing rows keep their values

## 3. Entities

- [x] 3.1 Lead and customer: use `HasAddress` in `Prospect`, merge `AddressRules::rules(true)` in `LeadRequest` and `CustomerRequest`, write the address through `Address::fromArray()` in `LeadRepository`, `CustomerRepository`, `LeadImport` and `CustomerImport`; verify with feature tests that `address_extra` is now saved, a field can be cleared and the existing lead/customer tests (filters, import, export) still pass
- [x] 3.2 Company: use `HasAddress`, `AddressRules` in `CompanySaveRequest` replacing the 255 limits, address in `CompanyRepository`; verify with feature tests saving the new fields and rejecting an 81 character street with a validation error
- [x] 3.3 Supplier: use `HasAddress`, `AddressRules` in the web `SupplierRequest` and the API `SupplierRequest`, address in `SupplierRepository` and `Api\Supplier\SupplierCreateController` (OpenAPI properties for the new fields); verify with web and API tests for city, new fields and an 81 character province

## 4. Views

- [x] 4.1 Create `components/address/form.blade.php` (search, country, province, city, locality, street, extra with placeholder, zipcode, hidden coordinates, map) and add the "Floor, office, suite..." key and the "Address extra" → "Dirección adicional" value to `lang/en.json` and `lang/es.json`; verify with a view test rendering it with an existing address and Spanish labels
- [x] 4.2 Replace the address sections of the lead, customer, company and supplier forms with the component (supplier loses its datalist); verify with feature tests that each form shows all address fields and the map and keeps existing values, and update the lead/customer update tests that assert "Dirección adicional (ej: Piso / Depto)"
- [x] 4.3 Create `components/address/display.blade.php` (`lines` / `inline`) and use it in `lead_customer/show`, `order/partial/_order` and `order/pdf` for company and customer; verify with feature tests that the order view and PDF HTML show street, zipcode and city, and that an empty address renders nothing

## 6. Contacts

- [x] 6.1 Create the migration renaming `country` to `country_id` and adding the rest of the address columns to `contact` and `supplier_contact`; use `HasAddress` in `Contact` and `SupplierContact`, update their `$fillable` and OpenAPI properties; verify with `migrate`/`migrate:rollback`, the `database-schema` tool and a model test
- [x] 6.2 Create `ContactSaveRequest` (`AddressRules`, lowercase country) for both contact save controllers, write the address in `ContactRepository` and `SupplierContactRepository`, and add the address form component to both contact forms; verify with feature tests saving a contact without address, with address and rejecting an 81 character street
- [x] 6.3 Show the country name and city columns in the lead/customer and supplier contact lists; verify with feature tests for a contact with and without address

## 5. Integration checks

- [x] 5.1 Run the lead, customer, company, supplier, order and translation test suites and `vendor/bin/pint --dirty --format agent`; verify they pass
