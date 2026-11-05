# Prospero Flow CRM browser extension

Saves the LinkedIn profile you are viewing as a contact of a lead or customer in the CRM.
Works in Chrome, Edge and Firefox from the same source. Plain JavaScript: no Node, npm or
build step.

It needs a CRM with the API this extension uses (`GET /api/country`, `search` on leads and
customers, `linkedin` on contacts), included since CRM version 5.57.28.

## Package

From the CRM root:

```bash
php artisan crm:extension:package
```

It checks that every locale has the English messages and writes
`storage/app/browser-extension/prospero-flow-crm-extension-<version>.zip`, with
`manifest.json` at the root and without `tests/` or this README. The version comes from `manifest.json`.
Use `--output=<directory>` to write it somewhere else.

The same zip installs in the three browsers and is the file the browser stores accept.

## Install

### Chrome and Edge

1. Unzip the package into a folder you keep: the browser loads the extension from it, so
   deleting or moving the folder removes the extension.
2. Open `chrome://extensions` (Chrome) or `edge://extensions` (Edge).
3. Turn on **Developer mode**.
4. Click **Load unpacked** and pick the unzipped folder.
5. Pin the extension from the puzzle icon of the toolbar so its icon stays visible.

### Firefox

Firefox only installs signed extensions for good. Until the extension is signed:

- **Temporary install** (any Firefox, removed when Firefox closes): open
  `about:debugging#/runtime/this-firefox`, click **Load Temporary Add-on…** and pick the
  zip or the `manifest.json` of the unzipped folder.
- **Permanent install without signing** (Firefox Developer Edition, Nightly or ESR only): set
  `xpinstall.signatures.required` to `false` in `about:config`, then open
  `about:addons`, click the gear icon, **Install Add-on From File…** and pick the zip
  renamed to `.xpi` (the file dialog only lists `.xpi` files).

## Use

1. Click the extension icon and log in with the CRM URL (for example
   `https://crm.example.com`), your email and password. Accept the browser request to access
   that URL. The password is not stored.
2. Open a LinkedIn profile (`linkedin.com/in/...`) and click **Add to CRM**.
3. Check the form, pick the lead or customer (or create a lead) and save.

## Update

1. Raise `version` in `manifest.json` (browsers and stores compare it).
2. Package again and replace the unzipped folder with the new one.
3. Click the reload icon of the extension in `chrome://extensions` / `edge://extensions`,
   or load the new zip again in Firefox.

## Develop

- Load the `browser-extension/` folder itself as unpacked (or as temporary add-on in
  Firefox) and reload the extension after each change.
- `tests/tests.html` checks the LinkedIn parsing. Open it from the loaded folder:
  `chrome-extension://<extension id>/tests/tests.html` (the id is shown in
  `chrome://extensions`) or `moz-extension://<uuid>/tests/tests.html` (the uuid is shown in
  `about:debugging`). The title starts with PASS when every check passes.
- When LinkedIn changes its markup and a field stops filling, fix the selectors in
  `LINKEDIN_SELECTORS` at the top of `linkedin.js` and add the case to `tests/tests.html`.
