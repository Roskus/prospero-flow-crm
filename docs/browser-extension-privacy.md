# Prospero Flow CRM browser extension: privacy policy

Last updated: 2026-11-07

The Prospero Flow CRM browser extension saves the LinkedIn profile you are viewing as a contact
of a lead or customer in your own Prospero Flow CRM. This policy explains which data the
extension handles, where it goes and how it is kept.

## Who receives the data

Prospero Flow CRM is open source and self-hosted. The extension only talks to the CRM URL you
enter when you log in. That CRM is run by you or by your company, and whoever runs it is
responsible for the data stored there. The authors of the extension do not run any server
for it and never receive, see or store your data.

## Data the extension handles

- **CRM login:** the CRM URL, your email and your password. The password is sent once to your
  CRM to log in and is never stored. The CRM answers with an access token.
- **Session:** the CRM URL, the access token and its expiry time, and your user and company
  names as returned by the CRM. They are kept so you do not have to log in on every use.
- **Profile data:** on a LinkedIn profile page, the extension reads the details shown on the
  page: name, job title, company and its LinkedIn URL, location and profile URL. It never reads
  photos or images. You can review and edit these details in the extension panel, and add an
  email and phone yourself, before saving.
- **Search terms:** the name you type to find the lead or customer the contact belongs to.
- **Cached lists:** the list of countries returned by your CRM, to fill the panel form.

## How the data is used

The data is used only to save the contact in your CRM, which is the single purpose of the
extension. Profile data is sent to your CRM only when you click "Add to CRM". The extension
does not read any page other than LinkedIn profile pages and does not track your browsing.

## Where the data is kept

The session, the last CRM URL used and the cached country list are kept in the browser's local
extension storage (`chrome.storage.local`), on your computer only. They are not synced to your
other devices. Logging out from the extension deletes the session and the cached lists.
Uninstalling the extension deletes everything it stored.

Profile data is not kept by the extension: once saved, it lives in your CRM.

## Sharing

The extension does not sell data, does not share it with third parties and does not use it for
advertising, analytics, profiling or credit decisions. It contains no analytics, tracking or
remote code. The only destination of any data is the CRM URL you entered.

## Security

The extension requires HTTPS for the CRM, except for a CRM running on your own computer
(`localhost` or `.test` domains). The access token is only used by the extension's background
script and is never exposed to web pages.

## Permissions

- `storage`: keep the session and settings described above.
- Access to your CRM domain: requested only for the CRM URL you enter, to call its API.
- LinkedIn pages: read the profile you are viewing and show the "Add to CRM" button and panel.

## Changes

Changes to this policy are published at this same address with a new update date.

## Contact

Questions about this policy can be asked at
https://github.com/Roskus/prospero-flow-crm/issues.
For data stored in a CRM, contact whoever runs that CRM.
