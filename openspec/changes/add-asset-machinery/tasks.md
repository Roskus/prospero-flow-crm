## 1. Types and data

- [x] 1.1 Excavator, generator and compressor types with code prefixes, labels and icons; `AssetType::isMachinery()` (verify: unit test of the machinery types and unique prefixes)
- [x] 1.2 `asset_machinery` migration, `AssetMachinery` model, factory and `Asset::machinery()` (verify: feature test saving a crane)

## 2. Form, detail and expiries

- [x] 2.1 Machinery card in the vehicle form, toggled by type; validation; detail removed for non-machinery types (verify: feature tests)
- [x] 2.2 Machinery data on the detail page; certification, calibration and safety inspection in the due dates, expiry state and "expiring" filter; search by certification number (verify: feature tests)
