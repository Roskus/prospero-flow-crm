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
`manifest.json` at the root and without `tests/` or this README. The package takes the CRM
version (`APP_VERSION` in `version.php`) as extension version, so every CRM release gives a
higher one; the `version` of `browser-extension/manifest.json` (`0.0.1`) only applies when the
folder is loaded directly for development.
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

Firefox 128 or newer. Firefox only installs signed extensions for good, so until the
extension is signed it is loaded as a temporary add-on, which Firefox removes when it closes.

#### Install locally, step by step

1. Package the extension from the CRM root (or skip this step and use the repository folder
   directly, handy while developing):

   ```bash
   php artisan crm:extension:package
   ```

2. Open `about:debugging#/runtime/this-firefox` in the address bar.
3. Click **Load Temporary Add-on…** and pick
   `storage/app/browser-extension/prospero-flow-crm-extension-<version>.zip`, or the
   `browser-extension/manifest.json` of the repository.
4. The extension appears under **Temporary Extensions**. Pin it: click the puzzle icon of the
   toolbar, open the extension menu (gear) and choose **Pin to Toolbar**.
5. Allow it on LinkedIn: open `about:addons`, click the extension, open the **Permissions**
   tab and turn on access to `www.linkedin.com`. Without it the **Add to CRM** button does
   not appear.
6. Log in: click the extension icon and enter the CRM URL, your email and password.
   - For a local CRM use its URL as you open it in the browser, for example
     `http://localhost:8000` or `https://prospero-flow-crm.test`. `http://` is only accepted
     for a CRM on this computer (`localhost`, `127.x`, `*.localhost` or `*.test`), so the
     password never travels unencrypted; any other CRM needs `https://`.
   - Firefox asks to allow access to that URL: click **Allow**. If the popup closes when the
     request appears, click the icon again and log in again; access is already granted.
7. Open a LinkedIn profile (`https://www.linkedin.com/in/...`), click **Add to CRM**, check
   the form and save.

After a code change, click **Reload** on the extension in `about:debugging`. After closing
Firefox, repeat steps 2 to 6.

When something fails, click **Inspect** on the extension in `about:debugging` to see the
console and network requests of the background, and use the browser console
(`Ctrl+Shift+J`) for the popup and the panel.

#### Permanent install without signing

Only in Firefox Developer Edition, Nightly or ESR: set `xpinstall.signatures.required` to
`false` in `about:config`, then open `about:addons`, click the gear icon, **Install Add-on
From File…** and pick the zip renamed to `.xpi` (the file dialog only lists `.xpi` files).
Steps 4 to 7 above still apply.

## Use

1. Click the extension icon and log in with the CRM URL (for example
   `https://crm.example.com`), your email and password. Accept the browser request to access
   that URL. The password is not stored.
2. Open a LinkedIn profile (`linkedin.com/in/...`) and click **Add to CRM**.
3. Check the form, pick the lead or customer (or create a lead) and save.

## Update

1. Package again: the zip takes the current CRM version.
2. Replace the unzipped folder with the new one.
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
