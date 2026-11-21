## Why

Companies using the CRM have no place to register the resources they own: the vehicle and machinery fleet (cars, cranes, motorcycles, lifts) and the IT equipment (PCs, notebooks, servers, mobiles, printers, routers). Today this is tracked in spreadsheets, so nobody knows reliably who holds each item, where it is, or when insurance, inspections or warranties expire.

## What Changes

- New **Inventory** module with two sections sharing one asset registry:
  - **Vehicles & machinery**: car, van, truck, motorcycle, crane, lift, forklift, other machinery.
  - **Devices**: desktop PC, notebook, server, mobile, tablet, printer, router, other device.
- Common asset data: internal code (unique per company), name, brand, model, serial number, status (available, assigned, in maintenance, retired), purchase date and price, supplier, branch where it is located, warranty end date, notes, and one optional photo (especially useful to identify machinery).
- Vehicle/machinery data: license plate, VIN/chassis number, manufacturing year, fuel type, odometer (km) or usage hours, insurance policy and expiry, technical inspection expiry.
- Device data: hostname, IP address, MAC address, operating system, technical specs (CPU, RAM, storage).
- Mobile phones: IMEI (required, unique per company), carrier the line is contracted with (chosen from a per-company carrier catalog managed in Settings), and associated mobile number.
- Assignment of an asset to a user, with full assignment history (who, from when, until when) and return.
- Maintenance log per asset (preventive/corrective/inspection, date, cost, description, next due date).
- Listing with filters (category, type, status, branch, assigned user, text search) and an "expiring soon" indicator for insurance, inspection, warranty and next maintenance.
- New permissions (`read asset`, `create asset`, `update asset`, `delete asset`), new `Inventory` module, new sidebar dropdown, English and Spanish translations.

## Capabilities

### New Capabilities
- `asset-inventory`: Company asset registry for vehicles/machinery and devices — CRUD, category-specific data, status lifecycle, listing, filtering and expiry indicators.
- `asset-assignment`: Assigning assets to users, returning them, and keeping the assignment history.
- `asset-maintenance`: Maintenance records per asset with cost and next due date.

### Modified Capabilities
<!-- None: no existing spec changes its requirements. -->

## Impact

- New tables: `carrier`, `asset`, `asset_vehicle`, `asset_device`, `asset_assignment`, `asset_maintenance` (all scoped by `company_id` through `asset`).
- New models, factories and PHP enums (`AssetCategory`, `AssetType`, `AssetStatus`, `MaintenanceType`, `FuelType`).
- New controllers under `app/Http/Controllers/Asset/` (single-action pattern), Form Requests, route file `routes/module/asset.php`.
- New Blade views under `resources/views/asset/` and `resources/views/carrier/`; Carriers tile in Settings; menu entry in `resources/views/menu/menu.php`.
- `ModuleSeeder` (new `Inventory` module) and `PermissionSeeder` (new permissions).
- Relations to existing `User`, `Branch`, `Supplier` and `Company` models (read-only use).
- Translations in `lang/en.json` and `lang/es.json`.
- No REST API in this change.
