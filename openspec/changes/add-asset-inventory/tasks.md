# Tasks

## 1. Data model

- [x] 1.1 Create enums `AssetCategory`, `AssetType` (with `category()` / `forCategory()`), `AssetStatus`, `MaintenanceType`, `FuelType` in `app/Enums/` with translated `label()`; verify with a unit test that every type maps to the right category
- [x] 1.2 Create migrations for `asset`, `asset_vehicle`, `asset_device`, `asset_assignment`, `asset_maintenance` with the indexes from design D5–D7; verify `php artisan migrate` and `migrate:rollback` run cleanly
- [x] 1.3 Create models `Asset`, `AssetVehicle`, `AssetDevice`, `AssetAssignment`, `AssetMaintenance` with casts, relations (`company`, `branch`, `supplier`, `vehicle`, `device`, `assignments`, `currentAssignment`, `maintenances`, user `withTrashed()`), `ofCompany` scope and inline `#[OAT\Schema]` properties; verify with a model feature test of relations and `currentAssignment`
- [x] 1.4 Create factories for all five models with `vehicle()`/`device()`, `assigned()`, `retired()` states; verify they are used successfully by the model test

## 2. Permissions, module and menu

- [x] 2.1 Add `Inventory` to `ModuleSeeder` and `read/create/update/delete asset` to `PermissionSeeder` with default roles (see design Open Questions); verify seeding twice is idempotent
- [x] 2.2 Add the "Inventory" dropdown (Vehicles & machinery, Devices) to `resources/views/menu/menu.php` gated by `read asset`; verify with a feature test that the menu is hidden without the permission
- [x] 2.3 Add translation keys to both `lang/en.json` and `lang/es.json` (types, statuses, fuel types, maintenance types, labels); verify the keys exist in both files

## 3. Carrier catalog

- [x] 3.1 Create `carrier` table/model/factory, `CarrierSaveRequest` (name unique per company among non-deleted), Index/Create/Update/Save/Delete controllers, `routes/module/carrier.php`, views and the Carriers tile in Settings; verify feature tests for CRUD, duplicate name, cross-company 404, permissions and soft delete keeping the name on devices

## 4. Asset CRUD and listings

- [x] 4.1 Create `routes/module/asset.php` with `{category}` constrained to `vehicle|device` and `->can()` per action, registered like sibling modules; verify `php artisan route:list --path=asset`
- [x] 4.2 Create `AssetSaveRequest` (type valid for category, code unique per company ignoring soft-deleted, branch/supplier of same company, IP/MAC format, IMEI required for mobile with 15 digits and unique per company, carrier (of the same company, not deleted) and phone number only for mobile/tablet, status with holder required for `assigned`) with custom messages; verify validation tests for each failure scenario in `specs/asset-inventory`
- [x] 4.3 Implement `AssetCreateController`, `AssetUpdateController`, `AssetSaveController` (saves asset + detail row in a transaction; retiring closes open assignment) and the form view with `_vehicle`/`_device` partials; verify feature tests for create, update, retire-assigned and cross-company 404
- [x] 4.4 Add optional photo: `photo`/`remove_photo` validation (image, jpg/png/webp, max 5 MB, `ExtensionMatchesContent`), storage on private disk under `asset/{company_id}/`, replace/remove deleting the old file, `AssetPhotoController` serving it company-scoped, thumbnail with category placeholder in listings and detail; verify with `Storage::fake()` feature tests for upload, replace, remove, invalid file and cross-company 404
- [x] 4.5 Implement `AssetIndexController` per category with filters (type, status, branch, assigned user, expiring soon, search incl. plate/hostname/IMEI/carrier/mobile number), default exclusion of retired, eager loading, pagination and expiry badges; verify feature tests for each listing/filter scenario
- [x] 4.6 Implement `expiringSoon()` scope and `expiryState()` helper on `Asset` (30-day window, expired vs expiring); verify unit/feature tests for expired, expiring and fine dates
- [x] 4.7 Implement `AssetShowController` (detail with category data, current holder, assignment and maintenance history) and `AssetDeleteController` (soft delete); verify feature tests for show, delete and code reuse after delete
- [x] 4.8 Verify permission tests: forbidden responses for each route without the matching permission

## 5. Assignment

- [x] 5.1 Create `AssetAssignRequest` / `AssetReturnRequest` (user active and same company, dates, end ≥ start); verify validation tests
- [x] 5.2 Implement `AssetAssignController` and `AssetReturnController` with transaction + row lock, status transitions and `assigned_by`; verify feature tests for assign, double-assign rejected, retired/in-maintenance rejected, return
- [x] 5.3 Add assign/return modals and history table to the show view; verify history ordering and deleted-holder name in a feature test

## 6. Maintenance

- [x] 6.1 Create `AssetMaintenanceSaveRequest` (type, date required, next due ≥ date, supplier same company); verify validation tests
- [x] 6.2 Implement maintenance save/update/delete controllers, recalculating `asset.next_maintenance_at` and raising vehicle odometer/usage hours only when higher; verify feature tests for each scenario in `specs/asset-maintenance`
- [x] 6.3 Add maintenance form, history table and per-currency totals to the show view; verify a feature test for totals after create and delete

## 7. Integration

- [x] 7.1 Run `vendor/bin/pint --dirty --format agent`, `vendor/bin/phpstan` on new files and `php artisan test --compact --filter=Asset`; verify all pass
- [x] 7.2 Run `openspec validate add-asset-inventory --strict`; verify it passes
- [x] 7.3 Bump `APP_VERSION` in `version.php` with the commit

## 8. Review changes

- [x] 8.1 Add `country_id` to carriers (form with company country as default, listing) and `CarrierSeeder` with the Spanish carriers, idempotent and respecting deleted ones; verify carrier and seeder tests
- [x] 8.2 Make every status selectable in the asset form, with a holder selector for `assigned` that opens, switches or closes assignments; verify feature tests for each transition and holder validation
- [x] 8.3 Add acquisition type purchase/renting with monthly fee and contract dates, renting end in the expiry indicators; verify feature and model tests
- [x] 8.4 Move the IMEI next to the serial number and add encrypted SIM PIN/PUK to the phone line, shown only with `update asset`; verify encryption, validation and visibility tests

- [x] 8.5 Add code generation per type, barcode-friendly code rule, exact-code/QR-URL search redirect, `asset.code` QR target and printable barcode/QR label; verify feature tests for generation, validation, scan redirect, cross-company 404 and label rendering

- [x] 8.6 Add assignment correction (holder, dates, notes) from the history, keeping open ones open and rejecting overlaps; verify feature tests

- [x] 8.7 Show the asset age on the detail (vehicles from the manufacturing year, others from purchase or renting start); verify model and view tests

## Workflow follow-up

- Archive the change after review and production verification.
