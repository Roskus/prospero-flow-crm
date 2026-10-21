# Proposal

## Why

Addresses are handled differently in every entity: lead and customer have the full form (country, province, city, locality, street, extra, zipcode, search and map), the company has no locality, extra or map, the supplier form has its own country datalist and hides the city it stores. Validation lengths do not match the columns (company street accepts 255 characters on a `varchar(80)` column) and lead/customer never save the extra address field. Addresses are printed by hand in each view and PDF, showing only the street. Branches and invoices are about to add more addresses, so the address has to be defined once and reused first.

## What Changes

- New `Address` value object with the address fields, formatting (one line and multiple lines) and conversion from and to arrays.
- New `HasAddress` trait: a model using it exposes `$model->address` as an `Address` read from and written to its own columns. No address table and no data migration: each entity keeps its columns.
- New `address()` schema macro defining the address columns and lengths in one place for new tables, plus a migration adding the missing columns to `company` (`locality`, `address_extra`, `latitude`, `longitude`) and `supplier` (`locality`, `address_extra`, `latitude`, `longitude`), and widening `province` to 80 characters in both.
- Shared address validation rules matching the column lengths, used by the lead, customer, company and supplier form requests, the supplier API and the lead/customer imports.
- Reusable address form component (search, country, province, city, locality, street, extra, zipcode and map) used in the lead, customer, company and supplier forms. The supplier form gets the city field and the shared country select.
- Reusable address display component used in the lead/customer detail, the order view and the order PDF, showing the full address instead of only the street.
- Contacts (of leads/customers and of suppliers) get a fully optional address with the same form and map. **BREAKING** (API): the unused `contact.country` / `supplier_contact.country` column is renamed to `country_id`, so contact API responses expose `country_id` instead of `country`. Nothing writes that column today.
- Contact lists show the country and city when the contact has them.
- Fix: lead and customer now save the extra address field.
- Translation of the extra field placeholder; label "Dirección adicional" with placeholder "Piso, oficina, depto...".

## Capabilities

### New Capabilities
- `address`: how an address is entered, validated, stored and displayed for leads, customers, companies, suppliers and contacts.

### Modified Capabilities

## Impact

- **Database**: new nullable columns on `company` and `supplier`; `province` widened to `varchar(80)` on both; `contact` and `supplier_contact` rename `country` to `country_id` and get the rest of the address columns. No data moves.
- **Code**: new `App\ValueObjects\Address` (new `app/ValueObjects` folder, approved) and `App\Traits\HasAddress`; schema macro registered in `AppServiceProvider`; `Prospect` (lead and customer), `Company`, `Supplier`, `Contact` and `SupplierContact` use the trait; `LeadRepository`, `CustomerRepository`, `CompanyRepository`, `SupplierRepository`, the supplier API controller and the imports write the address through it.
- **Views**: new `components/address/form` and `components/address/display`; lead, customer, company and supplier forms; lead/customer show; order view and PDF.
- **API**: supplier create accepts the new fields (`locality`, `address_extra`, `latitude`, `longitude`) and uses the shared lengths; existing fields keep their names.
- **Translations**: `lang/en.json` and `lang/es.json`.
- **Next changes**: `company-branches` creates the `branch` table with the macro and uses the trait and components; `accounting-invoicing` prints addresses with the display component.
