# Spec Delta

## Purpose

Lets a company keep a registry of the physical resources it owns — vehicles and machinery on one side, IT devices on the other — with the data needed to locate, identify and keep them compliant.

## ADDED Requirements

### Requirement: Asset categories and types
Every asset SHALL belong to exactly one category, `vehicle` (vehicles and machinery) or `device`, and to one type valid for that category. Vehicle types: car, van, truck, motorcycle, crane, lift, forklift, other. Device types: desktop, notebook, server, mobile, tablet, printer, router, other.

#### Scenario: Valid type for the category
- **WHEN** a user saves a vehicle asset with type `crane`
- **THEN** the asset is stored with category `vehicle` and type `crane`

#### Scenario: Type not valid for the category
- **WHEN** a user saves a device asset with type `crane`
- **THEN** the save is rejected with a validation error on the type field

### Requirement: Common asset data
Every asset SHALL store a name and an internal code, and MAY store brand, model, serial number, purchase date, purchase price with currency, supplier, branch, warranty end date and notes.

#### Scenario: Create asset with required data only
- **WHEN** a user with `create asset` permission saves an asset with category, type, name and code
- **THEN** the asset is created with status `available`

#### Scenario: Missing name or code
- **WHEN** a user saves an asset without name or without code
- **THEN** the save is rejected with a validation error on the missing field

### Requirement: Asset code unique per company
The internal code SHALL be unique among all the assets of the same company, deleted ones included, and SHALL only contain uppercase letters, digits and hyphens, so it can be printed as a Code 128 barcode and a QR code. Different companies MAY reuse the same code.

#### Scenario: Duplicate code in the same company
- **WHEN** a user saves an asset with a code already used by another asset of their company
- **THEN** the save is rejected with a validation error on the code field

#### Scenario: Same code in another company
- **WHEN** a user saves an asset with a code that only exists in a different company
- **THEN** the asset is saved

#### Scenario: Code with spaces
- **WHEN** a user saves an asset with code `CAR 1`
- **THEN** the save is rejected with a validation error on the code field

### Requirement: Code generation and fixed codes
The asset form SHALL offer a button that generates the next code of the selected type (type prefix plus a 5-digit sequence, e.g. `MOB-00001`), counting deleted assets. The code SHALL NOT change once the asset exists, so a printed label never points to another asset.

#### Scenario: Generate code
- **WHEN** a user picks type `mobile` and presses Generate, and the company already has `MOB-00007` (even deleted)
- **THEN** the code field is filled with `MOB-00008`

#### Scenario: Code is fixed after creation
- **WHEN** a user edits asset `CAR-00001` and changes its code to `CUSTOM`
- **THEN** the save is rejected with a validation error on the code field, since the code may be printed on labels

### Requirement: Search by code and printed labels
Searching an exact code in any asset listing, typed, read by a barcode scanner or as the URL of a label QR, SHALL open that asset of the company. The asset detail SHALL offer printing a 62 × 29 mm label with the code, name and company, either as a Code 128 barcode of the code or as a QR with the URL that opens the asset. The QR URL SHALL only resolve assets of the user's company.

#### Scenario: Scan a barcode
- **WHEN** a user scans the barcode `NB-00001` into the search box of the vehicles listing
- **THEN** the detail of notebook `NB-00001` opens

#### Scenario: Print QR label
- **WHEN** a user chooses Print label → QR code on an asset
- **THEN** a printable label with the QR, the code and the name opens

#### Scenario: QR of another company
- **WHEN** a user opens the QR URL of an asset of another company
- **THEN** the response is not found

### Requirement: Asset photo
Any asset SHALL accept one optional photo (JPG, PNG or WebP, max 5 MB, content matching its extension). Uploading a new photo SHALL replace the previous one and the user SHALL be able to remove it. The photo SHALL be shown in the asset detail and as a thumbnail in the listings, with a category placeholder when absent.

#### Scenario: Upload photo for a crane
- **WHEN** a user saves a crane with a JPG photo of 2 MB
- **THEN** the asset detail shows the photo and the vehicles listing shows its thumbnail

#### Scenario: Asset without photo
- **WHEN** a user saves an asset without photo
- **THEN** the asset is saved and the listing shows the category placeholder

#### Scenario: Replace photo
- **WHEN** a user uploads a new photo for an asset that already has one
- **THEN** the new photo is shown and the previous file is deleted from storage

#### Scenario: Remove photo
- **WHEN** a user removes the photo of an asset
- **THEN** the asset shows the placeholder and the file is deleted from storage

#### Scenario: Invalid file
- **WHEN** a user uploads a PDF, a file over 5 MB, or a file whose content does not match its image extension
- **THEN** the save is rejected with a validation error on the photo field

#### Scenario: Photo of another company
- **WHEN** a user requests the photo of an asset of another company
- **THEN** the system responds with not found

### Requirement: Vehicle and machinery data
A vehicle asset SHALL accept license plate, VIN/chassis number, manufacturing year, fuel type (gasoline, diesel, electric, hybrid, LPG, other), odometer in km, usage hours, insurance company, insurance policy number, insurance expiry date and technical inspection expiry date. These fields SHALL NOT be shown for device assets.

#### Scenario: Save vehicle data
- **WHEN** a user saves a car with plate `1234ABC`, fuel `diesel` and insurance expiry `2027-03-01`
- **THEN** the asset detail shows those values

#### Scenario: Machinery measured in hours
- **WHEN** a user saves a lift with usage hours `1500` and no odometer
- **THEN** the asset is saved with usage hours `1500`

### Requirement: Device data
A device asset SHALL accept hostname, IP address, MAC address, operating system, CPU, RAM, storage and other specs. IP and MAC addresses SHALL be validated for format when present. These fields SHALL NOT be shown for vehicle assets.

#### Scenario: Save device data
- **WHEN** a user saves a notebook with hostname `nb-ventas-01`, RAM `16 GB` and OS `Windows 11`
- **THEN** the asset detail shows those values

#### Scenario: Invalid IP address
- **WHEN** a user saves a router with IP address `999.1.1.1`
- **THEN** the save is rejected with a validation error on the IP address field

### Requirement: Mobile phone line data
A device of type `mobile` SHALL store its IMEI (required, 15 digits), the carrier the line is contracted with, chosen from the company carrier catalog, and the associated mobile number (both optional, since a phone may have no line yet). Tablets SHALL accept the same fields optionally. The IMEI SHALL be unique among non-deleted assets of the company. These fields SHALL NOT be shown for other device types.

#### Scenario: Save mobile with line
- **WHEN** a user saves a mobile with IMEI `356938035643809`, carrier `Movistar` and number `+34600123456`
- **THEN** the asset detail and the devices listing show the IMEI, carrier and number

#### Scenario: Carrier of another company
- **WHEN** a user saves a mobile with a carrier that belongs to another company
- **THEN** the save is rejected with a validation error on the carrier field

#### Scenario: Mobile without IMEI
- **WHEN** a user saves a mobile without IMEI
- **THEN** the save is rejected with a validation error on the IMEI field

#### Scenario: Invalid IMEI
- **WHEN** a user saves a mobile with IMEI `12345`
- **THEN** the save is rejected with a validation error on the IMEI field

#### Scenario: Duplicate IMEI
- **WHEN** a user saves a mobile with an IMEI already registered on another asset of their company
- **THEN** the save is rejected with a validation error on the IMEI field

#### Scenario: Mobile without line
- **WHEN** a user saves a mobile with IMEI but no carrier or number
- **THEN** the asset is saved

#### Scenario: Search by number
- **WHEN** a user searches `600123456` in the devices listing
- **THEN** the mobile with that associated number is shown

#### Scenario: IMEI next to the device identifiers
- **WHEN** a user opens the form of a mobile or tablet
- **THEN** the IMEI field is shown next to the serial number, and the phone line section holds the carrier, number and SIM codes

### Requirement: SIM codes
A mobile or tablet line SHALL optionally store the SIM PIN (4 to 8 digits) and PUK (8 digits). Both SHALL be stored encrypted with the application key, SHALL NOT be included in serialized output, SHALL NOT be searchable, and SHALL only be shown, masked until revealed, to users with `update asset` permission.

#### Scenario: Store SIM codes encrypted
- **WHEN** a user saves a mobile with PIN `1234` and PUK `12345678`
- **THEN** the database holds both values encrypted and the asset detail reveals them on demand

#### Scenario: Invalid PUK
- **WHEN** a user saves a mobile with PUK `1234`
- **THEN** the save is rejected with a validation error on the PUK field

#### Scenario: Read-only user
- **WHEN** a user with only `read asset` opens the detail of a mobile with a PUK
- **THEN** the PIN and PUK are not shown

### Requirement: Carrier catalog
Each company SHALL manage its own list of mobile carriers (name required, the name and country pair unique among the company's non-deleted carriers, optional ISO 3166-1 alpha-2 country defaulting to the company country, optional phone and website) from the Settings page. Listing requires `read asset`, creating and editing require `update asset`, deleting requires `delete asset`.

#### Scenario: Create carrier
- **WHEN** a user with `update asset` permission creates carrier `Movistar` in Settings
- **THEN** `Movistar` appears in the carrier list and in the carrier selector of mobile devices

#### Scenario: Duplicate carrier name
- **WHEN** a user creates a carrier with a name already used by another carrier of their company in the same country
- **THEN** the save is rejected with a validation error on the name field

#### Scenario: Carriers of another company
- **WHEN** a user opens the carrier list or edits a carrier id of another company
- **THEN** carriers of other companies are not listed and editing responds with not found

### Requirement: Carrier seed by country
A seeder, run on every deploy, SHALL load the well-known mobile carriers of each company's country (Spain, Argentina, Mexico, Panama and the United Kingdom for now), skipping carriers the company already has or deleted.

#### Scenario: Seed carriers of the company country
- **WHEN** the carrier seeder runs twice for a company in Spain
- **THEN** the company has Movistar, Orange, Vodafone, Digi and the other Spanish carriers once each, with country `es`

### Requirement: Deleting a carrier keeps device history
Deleting a carrier SHALL be soft: it SHALL no longer be selectable for devices, while devices that already reference it SHALL keep showing its name.

#### Scenario: Delete carrier in use
- **WHEN** a user with `delete asset` permission deletes carrier `Orange` used by a mobile
- **THEN** `Orange` is not offered in the carrier selector and the mobile detail still shows `Orange`

### Requirement: Asset status lifecycle
An asset SHALL have one status: `available`, `assigned`, `in_maintenance` or `retired`, all selectable in the asset form. Choosing `assigned` SHALL require an active user of the company as holder and opens an assignment for that user; choosing another holder closes the open assignment and opens a new one; choosing any other status closes the open assignment with today as end date. A retired asset SHALL be excluded from the default listing.

#### Scenario: Retire an asset
- **WHEN** a user with `update asset` permission sets an available asset to `retired`
- **THEN** the asset no longer appears in the default listing but appears when filtering by status `retired`

#### Scenario: Assign from the form
- **WHEN** a user saves an asset with status `assigned` and holder Ana
- **THEN** an assignment for Ana starting today is opened and the asset status is `assigned`

#### Scenario: Assigned without holder
- **WHEN** a user saves an asset with status `assigned` and no holder
- **THEN** the save is rejected with a validation error on the holder field

#### Scenario: Retire an assigned asset
- **WHEN** a user sets an assigned asset to `retired`
- **THEN** the open assignment is closed with today as end date and the asset becomes `retired`

### Requirement: Purchase or renting
An asset SHALL be acquired by `purchase` (default; purchase date and price) or `renting` (monthly fee, contract start and end dates, and the supplier acting as renting company). Only the data of the chosen acquisition type SHALL be kept. The renting end date SHALL NOT be earlier than its start and SHALL feed the expiry indicators.

#### Scenario: Save a renting vehicle
- **WHEN** a user saves a car as `renting` with fee 450, start `2026-01-01` and end `2029-12-31`
- **THEN** the asset detail shows the renting contract and no purchase data

#### Scenario: Renting ending soon
- **WHEN** the renting contract of an asset ends within 30 days
- **THEN** the asset is flagged as expiring and appears with the expiring filter

### Requirement: Asset age
The asset detail SHALL show the asset age in years and months. Vehicles with a manufacturing year SHALL age from January 1st of that year, since they are often bought used; other assets, and vehicles without that year, SHALL age from the purchase date or, for rented assets, the renting start. When no date is known it SHALL say so.

#### Scenario: Age from purchase
- **WHEN** on `2026-11-21` a user opens a notebook purchased on `2023-05-03`
- **THEN** the detail shows age `3 years 6 months` since `03/05/2023`

#### Scenario: Used vehicle
- **WHEN** on `2026-11-21` a user opens a car manufactured in 2006 and purchased on `2019-10-01`
- **THEN** the detail shows age `20 years 10 months`, manufactured in 2006

#### Scenario: Unknown age
- **WHEN** a user opens a device without purchase or renting start date
- **THEN** the detail says the age is unknown and suggests adding the purchase date

### Requirement: Company scoping
Assets SHALL be visible and editable only within the company that owns them. Referenced branch, supplier and assigned user SHALL belong to the same company.

#### Scenario: Access another company's asset
- **WHEN** a user opens the edit page of an asset of another company
- **THEN** the system responds with not found

#### Scenario: Branch from another company
- **WHEN** a user saves an asset with a branch of another company
- **THEN** the save is rejected with a validation error on the branch field

### Requirement: Separate listings per category
The system SHALL provide one listing for vehicles and machinery and another for devices, each paginated and showing code, name, type, brand/model, status, branch and assigned user.

#### Scenario: Vehicle listing
- **WHEN** a user with `read asset` permission opens the vehicles listing
- **THEN** only non-retired vehicle assets of their company are shown

#### Scenario: Device listing
- **WHEN** a user with `read asset` permission opens the devices listing
- **THEN** only non-retired device assets of their company are shown

### Requirement: Listing filters
Each listing SHALL be filterable by type, status, branch and assigned user, and searchable by code, name, brand, model, serial number, license plate (vehicles) or hostname, IMEI, carrier or mobile number (devices).

#### Scenario: Search by plate
- **WHEN** a user searches `1234ABC` in the vehicles listing
- **THEN** only the vehicle with that license plate is shown

#### Scenario: Filter by assigned user
- **WHEN** a user filters the devices listing by assigned user Ana
- **THEN** only devices currently assigned to Ana are shown

### Requirement: Expiry indicators
The listing and detail SHALL flag an asset whose insurance, technical inspection, warranty or next maintenance date is past (expired) or within the next 30 days (expiring soon). The listing SHALL offer an "expiring soon" filter.

#### Scenario: Inspection expiring soon
- **WHEN** a vehicle's inspection expires in 10 days
- **THEN** the listing shows an "expiring soon" badge on it and the asset appears when filtering by expiring soon

#### Scenario: Insurance expired
- **WHEN** a vehicle's insurance expired yesterday
- **THEN** the listing shows an "expired" badge on it

### Requirement: Delete asset
A user with `delete asset` permission SHALL be able to delete an asset. Deletion SHALL be soft, keeping its assignment and maintenance history.

#### Scenario: Delete
- **WHEN** a user with `delete asset` permission deletes an asset
- **THEN** the asset disappears from the listings and its code stays reserved, so its label never opens another asset

### Requirement: Permissions
Inventory actions SHALL require permissions: `read asset` to view, `create asset` to create, `update asset` to edit, assign and log maintenance, `delete asset` to delete. The Inventory menu SHALL be shown only to users with `read asset`.

#### Scenario: User without permission
- **WHEN** a user without `read asset` opens the vehicles listing
- **THEN** the system responds with forbidden and the Inventory menu is hidden
