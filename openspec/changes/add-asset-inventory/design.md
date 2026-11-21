# Design

## Context

See proposal.md (Why). The app uses single-action controllers per module (`app/Http/Controllers/<Module>/<Module>{Index,Create,Update,Save,Delete}Controller`), one route file per module in `routes/module/`, Form Requests, Spatie permissions seeded in `PermissionSeeder` (linked to a `module` row from `ModuleSeeder`), Blade views, and a sidebar defined in `resources/views/menu/menu.php` with dropdown `children`. Every query is scoped by `company_id`; child tables reach the company through their parent. Existing models reused: `User`, `Branch`, `Supplier`, `Company`. Requirements live in `specs/`.

## Goals / Non-Goals

**Goals:**
- One asset registry with category-specific data that fits both fleets and IT equipment.
- Traceable assignment and maintenance history.
- Follow existing module conventions so the code looks like its siblings.

**Non-Goals:**
- REST API and browser extension support (later change).
- File attachments (invoices, insurance PDFs) and photo galleries — later change. Only one photo per asset now.
- Notifications/emails for expiring dates; only visual indicators and filter.
- Depreciation/accounting entries, fuel consumption tracking, GPS/telematics, network discovery of devices.
- Consumables / stock quantities (this is unit-tracked inventory, one row per physical item).
- Assigning assets to customers, departments or branches as holders (holder is always a user; branch is location).

## Decisions

### D1: Base `asset` table + 1:1 detail tables per category
`asset` holds common fields and `category`/`type`/`status`; `asset_vehicle` and `asset_device` hold category-specific columns keyed by `asset_id`.
- *Alternative: single wide table with nullable columns* — simpler queries, but ~20 nullable columns that only apply to half the rows and blurry validation.
- *Alternative: JSON `attributes` column* — flexible but not searchable/indexable for plate, IMEI, hostname, and loses typed validation.
- *Alternative: two fully independent modules (vehicles, devices)* — duplicates assignment, maintenance, listing and permission code.
The base table keeps assignment/maintenance generic (FK to `asset.id`) while detail tables keep columns meaningful.

### D2: Categories, types and statuses as backed PHP enums
`AssetCategory` (`vehicle`, `device`), `AssetType` (string-backed, with `category(): AssetCategory` and `forCategory()`), `AssetStatus`, `MaintenanceType`, `FuelType` in `app/Enums/`, with `label()` returning translated text (same style as `LeadStatus`). Stored as `string` columns, not DB enums, so new types need no migration.
- *Alternative: catalog tables editable by the user* — not needed yet; enum + `other` covers the stated list.

### D3: Routes and UI split by category, shared controllers
URL prefix is `/inventory` (not `/asset`: `public/asset/` already exists for static files and uploads, and the web server would serve or shadow it). Routes `/inventory/vehicle` and `/inventory/device` (index, create), shared `/inventory/update/{id}`, `/inventory/show/{id}`, `/inventory/save`, `/inventory/delete/{id}`, `/inventory/photo/{id}`, `/inventory/assign/{id}`, `/inventory/return/{id}`, `/inventory/maintenance/...`. Controllers receive the category from the route (`{category}` constrained to `vehicle|device`) and the Blade form includes a `_vehicle` or `_device` partial. One menu dropdown "Inventory" with two children.

### D4: Status ownership
The edit form offers every status. `assigned` requires a holder (`assigned_user_id`, an active company user, or the current holder even if deactivated); saving syncs the assignment in the same transaction: same holder keeps it, a different holder closes it (today) and opens a new one, any other status closes it. The assign/return actions on the detail page remain as shortcuts and run in a DB transaction with a row lock on the asset to prevent double assignment.

### D10: Purchase or renting
`asset.acquisition_type` (`purchase` default | `renting`) with `rental_monthly_fee`, `rental_starts_at`, `rental_ends_at` on the base table; the supplier doubles as renting company. Saving nulls the fields of the other type. `rental_ends_at` is one more due date for the expiry indicators.

### D11: SIM codes
`asset_device.sim_pin` / `sim_puk` are `text` columns with Laravel's `encrypted` cast and listed in `$hidden`. They depend on `APP_KEY`: rotating it needs `APP_PREVIOUS_KEYS` so existing values can still be decrypted. Shown masked, revealed client-side, only to `update asset`.

### D5: Assignment history table
`asset_assignment` (`asset_id`, `user_id`, `assigned_by`, `start_date`, `end_date` nullable, `notes`). Open assignment = `end_date IS NULL`. The `user` relation uses `withTrashed()` so deleted holders still show. `asset.assigned_user_id` is **not** stored; the current holder is derived from the open assignment via a `currentAssignment()` `HasOne` (`ofMany`) relation, eager loaded in listings.
- *Alternative: denormalized `assigned_user_id` on asset* — faster filter, but two sources of truth. Revisit if listings get slow.

### D6: Expiry computation
"Expiring soon" window = 30 days, constant on the model. Computed in a query scope `expiringSoon()` over `asset_vehicle.insurance_expires_at`, `asset_vehicle.inspection_expires_at`, `asset.warranty_until` and `asset.next_maintenance_at`. `next_maintenance_at` is denormalized on `asset` and recalculated whenever a maintenance record is created, updated or deleted (latest record by date), so the scope stays a simple indexed query.

### D7: Code uniqueness
Unique index `(company_id, code, deleted_at)` is unreliable with NULLs in MySQL, so uniqueness is enforced in the Form Request (`Rule::unique('asset')->where(company_id)->whereNull('deleted_at')->ignore($id)`), plus a non-unique index on `(company_id, code)`.

### D12: Codes, barcodes and labels
Codes match `^[A-Z0-9-]+$`, readable by any scanner keyboard layout and valid in Code 128 and in a URL. `AssetCodeService` generates `PREFIX-00001` per type (max numeric suffix including soft-deleted assets), resolves scans (plain code or `/inventory/code/{code}` URL) and renders SVGs: Code 128 with the existing `picqer/php-barcode-generator`, QR with `bacon/bacon-qr-code` (added, approved by the user). The QR holds the absolute `asset.code` URL so a phone opens the asset after login; it depends on `APP_URL`. Labels are a standalone printable page at 62 × 29 mm, the common thermal roll size.

### D8: Single photo stored on a private disk, served through a controller
`asset.photo_path` (nullable) holds the path on the `local` (private) disk under `asset/{company_id}/{uuid}.{ext}`, stored with `$file->store()`. It is served by `AssetPhotoController` (`GET /inventory/photo/{id}`, `read asset`), which loads the asset scoped by company and returns the file, so photos of one company are not reachable by URL guessing from another. Validation: `['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', new ExtensionMatchesContent]` (rule already used by `ProfileSaveRequest`), plus a `remove_photo` boolean. Replacing or removing deletes the old file after the DB commit; soft delete keeps the file (history), force delete is not offered.
- *Alternative: `public` disk like Payroll* — simpler `<img src>`, but files are publicly reachable by URL and not company-scoped.
- *Alternative: legacy `public/asset/upload/company/<slug>` used by profile photos* — unscoped, name-collision prone; not reused.
- *Alternative: generic polymorphic attachments table* — overkill for one photo; can be introduced when attachments are added.

### D9: Carrier catalog per company
`carrier` table (`company_id`, `name`, `phone`, `website`, soft deletes) with Index/Create/Update/Save/Delete controllers under `app/Http/Controllers/Carrier/`, routes `/carrier/...` in `routes/module/carrier.php`, and a "Carriers" tile in the Inventory group of `setting/index.blade.php`. `asset_device.carrier_id` is a nullable FK; the device relation uses `withTrashed()` so deleted carriers keep showing, while selectors only list non-deleted ones. Permissions reuse the asset ones (no new permission names).
- *Alternative: free-text carrier* — inconsistent spellings make filtering and reporting by carrier unreliable.
- *Alternative: global (not per company) catalog* — carriers differ per country; each company curates its own.

## Data Model

```
carrier
├── id, company_id (FK, index), name, phone, website (nullable)
└── timestamps, softDeletes

asset
├── id, company_id (FK, index)
├── category (string), type (string), status (string, default 'available')
├── code (string 50), name, brand, model, serial_number (nullable)
├── branch_id (FK nullable), supplier_id (FK nullable)
├── purchase_date (date), purchase_price (decimal 12,2), currency (char 3) — nullable
├── warranty_until (date nullable), next_maintenance_at (date nullable)
├── notes (text nullable)
├── photo_path (string nullable)
└── timestamps, softDeletes

asset_vehicle (asset_id PK/FK)
├── license_plate, vin, manufacturing_year (smallint), fuel_type
├── odometer_km (unsigned int), usage_hours (unsigned int)
├── insurance_company, insurance_policy, insurance_expires_at (date)
└── inspection_expires_at (date)

asset_device (asset_id PK/FK)
├── hostname, ip_address (45), mac_address (17), operating_system
├── imei (15, index), carrier_id (FK nullable), phone_number (E.164, index)   — mobile/tablet only
└── cpu, ram, storage, specs (text)

asset_assignment
├── id, asset_id (FK), user_id (FK), assigned_by (FK user)
├── start_date, end_date (nullable), notes
└── timestamps

asset_maintenance
├── id, asset_id (FK), type, date, description
├── cost (decimal 12,2 nullable), currency (char 3 nullable), supplier_id (FK nullable)
├── odometer_km, usage_hours (nullable), next_due_at (date nullable)
└── created_by (FK user), timestamps
```

## Risks / Trade-offs

- [Detail tables add joins to listings/search] → eager load `vehicle`/`device`; search uses `whereHas` only on the detail table of the listing's category; index plate, IMEI and hostname.
- [Derived current holder makes "filter by user" a subquery] → index `asset_assignment(asset_id, end_date)` and `(user_id, end_date)`; denormalize later if needed.
- [Enum types may not cover every company's machinery] → `other` type plus free-text name/model.
- [Denormalized `next_maintenance_at` can drift] → recalculated in one model method called from create/update/delete of maintenance, covered by tests.

- [Photos served through PHP instead of directly by the web server] → small files, `Cache-Control: private, max-age` headers; listing thumbnails use `loading="lazy"`.
- [Orphan files if the DB transaction fails after storing] → store the file, and delete it in the catch block when the transaction rolls back.

## Migration Plan

Additive only: new tables, new module row, new permissions. `deploy.sh` runs `php artisan migrate` and then `ModuleSeeder` and `PermissionSeeder`. A migration first merges the duplicate module rows that earlier `ModuleSeeder` runs created and adds a unique index on `module.name`, so the seeder is idempotent on every deploy. Clear the permission cache afterwards. Rollback: drop the six new tables and remove the permissions; no existing data is touched.

## Open Questions

- Which roles get each permission by default (proposed: admin/superadmin all four; manager `read` + `update`; seller none). Can be adjusted in `PermissionSeeder` without affecting specs.
