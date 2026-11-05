# Tasks

## 1. API: token lifetime

- [x] 1.1 Make `jwt.ttl` fall back to the 60 minutes default when `JWT_TTL` is missing or empty (`(int) env('JWT_TTL', 60) ?: 60`, so tokens always expire) and fix `LoginController::respondWithToken()` so `expires_in` is that TTL × 60 seconds; verify with feature tests on `POST /api/auth/login` and `POST /api/auth/refresh` for the default (3600) and a custom `jwt.ttl` of 15 (900)

## 2. API: search leads and customers

- [x] 2.1 Add a `search()` scope on `Prospect` (name or business name contains the text, `%`/`_` escaped, no-op when empty) and verify with a model test for name, business name, case insensitivity and a literal `%`
- [x] 2.2 Add `ProspectListRequest`, shared by both lists (optional `search` string max 120, `per_page`, `page` integers) and apply `search()` before pagination in `LeadListController` and `CustomerListController`; verify with feature tests: matching by name and business name, `count` equals the matches, no search returns every lead, a lead of another seller is found, a lead/customer of another company is not, `next_page_url` keeps `search` and `per_page`, and a 121 character search returns 422
- [x] 2.3 Add a shared `Search` OpenAPI query parameter and reference it from both lists; verify by extending `DocGeneratorControllerTest` so `/lead` and `/customer` document `search`

## 3. API: contact lookup by LinkedIn URL

- [x] 3.1 Add a LinkedIn profile URL normalizer (profile slug `/in/<slug>`, lower case, without scheme, `www.`, query or trailing slash; null when not a profile URL) and verify with a unit test covering `https://www.linkedin.com/in/Jane-Doe/`, `linkedin.com/in/jane-doe?trk=x`, `http://es.linkedin.com/in/jane-doe` and a company URL returning null
- [x] 3.2 Add `ContactListRequest` (optional `linkedin` string max 255) and filter `ContactListController` by the normalized slug (end, `/` or `?` after it, escaped `LIKE`), keeping `{count, contacts}`; verify with feature tests: different URL forms find the same contact, `jane-doe` does not match `jane-doe-2`, no match returns `count` 0, a contact of another company is not listed, and the response carries `lead_id`/`customer_id`
- [x] 3.3 Document the `linkedin` query parameter on `GET /contact`; verify by extending `DocGeneratorControllerTest`

## 4. Extension scaffold and session

- [ ] 4.1 Create `browser-extension/` with `manifest.json` (MV3, `default_locale` en, `background.service_worker` + `background.scripts` to `background.js`, `browser_specific_settings.gecko`, `storage` permission, `optional_host_permissions`, content scripts `linkedin.js` + `content.js` and `web_accessible_resources` for `https://www.linkedin.com/*`), `_locales/en` and `_locales/es` with the same keys and the icons; verify it loads without manifest errors in Chrome, Edge and Firefox
- [ ] 4.2 Implement the session in `background.js` (CRM URL and token in `chrome.storage.local`, `expires_at`, refresh 5 minutes before expiry and once on 401, clear session on failed refresh or `403 Inactive user`, JSON errors with field messages) and its message handlers (`login`, `logout`, `session`, `searchOwners`, `findContactByLinkedin`, `createLead`, `createContact`); verify manually against a local CRM with a short `JWT_TTL` (e.g. 2 minutes): the session survives expiry through refresh, a blacklisted token sends the user to login and an inactive user is logged out
- [ ] 4.3 Build the popup (CRM URL, email, password; `chrome.permissions.request` for the CRM origin on submit; warning for non `https` URLs; shows user and company from `me` and `GET /api/company/{id}`; logout) translated with `_locales`; verify manually in the three browsers: successful login, wrong password, denied permission, unreachable URL, logout revokes the token (a request with the old token gets 401)

## 5. LinkedIn capture

- [ ] 5.1 Implement in `linkedin.js` the profile URL normalization (same rules and cases as 3.1) and the location split (`City, Province, Country`, `City, Country`, `Country` → city, province, ISO country, English and Spanish country names); add the cases to `tests/tests.html` and verify they all pass when the page is opened from the loaded extension, including an unknown country left empty
- [ ] 5.2 Implement the profile reader in `linkedin.js` with the selector table and per-field fallbacks (name split, headline `at`/`|`/`@` company, location, current company from the top card or first experience), never reading images; add anonymised fixtures without images (full profile, no location, headline without company) to `tests/tests.html` and verify they pass
- [ ] 5.3 Implement `content.js`: detect `/in/<slug>` on load and on SPA navigation, show the "Add to CRM" button only there, read the profile on click and open the panel iframe with it; ask to log in when there is no session; verify manually: button on a profile, not on feed/search/company pages, appears after navigating from the feed to a profile without reload, nothing is read before the click

## 6. Contact panel

- [ ] 6.1 Build the panel (form with first name required, last name, job title, company, country select, province, city, LinkedIn URL, optional email and phone, owner search over leads and customers prefilled with the company, "Create lead" option; no photo anywhere) translated with `_locales`; verify manually that the form opens prefilled from a profile, missing fields stay empty and every field is editable
- [ ] 6.2 Show the duplicate warning from `findContactByLinkedin` with the owner name and a link to the lead or customer in the CRM, still allowing a new contact; verify manually with a profile already saved and with a new one
- [ ] 6.3 Save: create the lead first when chosen (`name`, `seller_id` from `me`, form or company country), then `POST /api/contact`; show a confirmation with a link to the owner; show CRM validation errors on their fields keeping the typed values; verify manually: contact saved to an existing lead, to a customer, to a new lead, and a too long job title shows the field error

## 7. Integration checks

- [ ] 7.1 Run the end-to-end flow in Chrome, Edge and Firefox against a local CRM (login, capture, duplicate warning, save to lead, customer and new lead, logout) and verify in the CRM that each contact has the LinkedIn URL, job title and location and no photo; confirm with the browser network panel that captured data is only sent to the CRM origin and no image is requested by the extension
- [ ] 7.2 Run `php artisan test --compact tests/Feature/Controllers/Api`, open `tests/tests.html` from the loaded extension, run `vendor/bin/pint --dirty --format agent` and bump `version.php`; verify everything passes

## Workflow follow-up

- Archive the change after the pull request is reviewed and merged.
