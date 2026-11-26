# Proposal

## Why

Contractors (oil and gas services, construction, maintenance) must keep each machine's certification, calibration and periodic safety inspection valid to work on a client's site. The inventory already records machinery as vehicle types with usage hours, but has nowhere to store these documents' numbers and due dates, so expiring certificates go unnoticed. See issue #303, part of #311.

## What Changes

- New machinery types in the vehicle category: excavator, generator and compressor (codes EXC, GEN, CMP).
- An `asset_machinery` detail, one row per machine (crane, lift, forklift, excavator, generator, compressor, other machinery): capacity, certification number, certifying body, and the certification, calibration and safety inspection expiry dates.
- The machinery card shows in the vehicle form only for machinery types; changing a machine to a non-machinery type removes its machinery detail.
- The three dates feed the existing expiry indicators and the "expiring" filter; the listing search also matches the certification number.

Out of scope: a separate menu or category for machinery, document files and alerts (issues #304 and #305).

## Capabilities

### Modified Capabilities
- `asset-inventory`: machinery types and machinery data.

## Impact

- Database: new `asset_machinery` table.
- Code: `AssetType::isMachinery()`, `AssetMachinery` model and factory, `AssetService`, `AssetSaveRequest`, asset views and controllers.
- Translations: new keys in `lang/en.json` and `lang/es.json`.
- Tests: `AssetMachineryTest`, `AssetTypeTest`.
