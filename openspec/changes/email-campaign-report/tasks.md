# Tasks

## 1. Data

- [x] 1.1 Migration adding `campaign.name` (nullable, backfill from `subject`, then not null) and the `campaign_recipient` table; `CampaignRecipient` model with status constants, factory and `Campaign::recipients()`; `name` in `Campaign` fillable and OpenAPI schema; verify with a migration backfill test and a model relation test

## 2. Campaign form and delete

- [x] 2.1 `CampaignSaveRequest` (name, subject, allowed from, body, schedule) used by `CampaignSaveController`; name field in the form and name column in the list; verify with tests for saving, missing name, wrong from and another company's campaign
- [x] 2.2 `delete campaign` permission in `PermissionSeeder`, `DELETE /campaign/{id}` route with a confirm form in the list and the dead "Send" button removed; verify with tests for deleting, forbidden and another company's campaign

## 3. Sending

- [x] 3.1 Rewrite `CampaignSender`: due campaigns by date and time not yet sent, leads by company with email and opt in in chunks, recipient row before sending, `GenericEmail` with the right arguments, SMTP failures stored as bounced, `send_at` and `emails_count` at the end; `withoutOverlapping()` on the schedule; verify with tests for a due campaign, not yet due, already sent, resume without duplicates, other companies and opt out leads, and a failing recipient (mail fake throwing)
- [x] 3.2 Tracking pixel: signed public route `campaign.open`, `CampaignOpenController` returning a transparent GIF and recording the open, tracking image in `mail.generic` only when a URL is given; verify with tests for the first open, a repeated open, a tampered signature and the image in the sent email

## 4. Report

- [x] 4.1 `Campaign::getEmailReport()` and `ReportEmailController` with the report view (name, send date, sent, delivered, opens, bounced, open rate, bounce rate, totals, empty state, estimate note); verify with tests for the figures and rates, zero divisors, unique opens, unsent and deleted campaigns, other companies and forbidden access

## 5. Translations and checks

- [x] 5.1 Add every new key to `lang/en.json` and `lang/es.json`; verify with a key coverage test over the campaign and email report views
- [x] 5.2 Run the full suite, `vendor/bin/pint --dirty --format agent`, Larastan on the changed files and `openspec validate email-campaign-report --strict`; verify everything passes
