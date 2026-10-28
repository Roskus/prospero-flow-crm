# Design

## Context

- `campaign (company_id, subject, from, body, tags, schedule_send_date, schedule_send_time, send_at, emails_count, soft deletes)`; no name and no per recipient data.
- `CampaignSaveController` saves the request without validation. `CampaignDeleteController` exists, scoped by company, but has no route; the list calls `/campaign/delete/{id}` with a GET and also renders a "Send" button whose `Campaign.send` function does not exist.
- `crm:campaign:send` runs every 15 minutes (`bootstrap/app.php`). It selects campaigns by `schedule_send_date = today` only, so a campaign is sent again on every run of its day, and it calls `new GenericEmail($company, $subject, $data)` while the constructor is `(Company, ?User, ?string $subject, ?array $data)`, which fails with a type error.
- Recipients are `lead` rows of the campaign company with an email and `opt_in`. The `tags` of a campaign are not used to select recipients today; that stays as it is.
- `mail.generic` renders `{!! $body !!}` and an optional signature. The mail driver is SMTP; `Mail::send()` throws on a rejection by the server.
- `ReportEmailController` renders an empty `report.email`; the route uses `can('read report')` and the reports hub already lists it under Communication.

## Goals / Non-Goals

**Goals:**
- Reliable one time sending with a durable per recipient record that the report aggregates in SQL.
- Opens recorded by a public endpoint that cannot be used to touch other recipients.

**Non-Goals:**
- Provider webhooks for late bounces and complaints (later change).
- Click tracking, unsubscribe links, queued sending, selecting recipients by tags, replacing `{{ $prospect->first_name }}` variables in the body.

## Decisions

### Schema
- `campaign.name` string 255, added nullable, backfilled with `subject`, then made not null. Down drops it.
- `campaign_recipient`: `id`, `campaign_id` (index, FK to campaign, cascade on delete), `lead_id` nullable, `email` 254, `status` string 20 (`sent`|`bounced`), `error` text nullable, `sent_at` datetime, `opened_at` datetime nullable, `open_count` unsigned int default 0, timestamps; unique `(campaign_id, email)`.
- `campaign.schedule_at` datetime (application time zone, UTC), computed on save from `schedule_send_date` + `schedule_send_time` read in the saving user's `timezone` (like `Calendar`); the date and time columns keep what the user typed. Existing campaigns are backfilled with date + time as they were compared before (application time zone). Index `(send_at, schedule_at)`.
- No `company_id` on the child table: it is always reached through `campaign.company_id` (project rule for child tables).
- Status values as constants on `CampaignRecipient` (`STATUS_SENT`, `STATUS_BOUNCED`), like other models of the project.

### Sender
- Due campaigns: `whereNull('send_at')` and `schedule_at <= now()`.
- Leads read with `chunkById(200)`, scoped by `company_id`, `whereNotNull('email')`, `opt_in = true`.
- For each lead, skip the email if a recipient row for `(campaign_id, email)` already exists (resume after a crash). Otherwise create the row first with `status = sent`, build `GenericEmail($company, null, $campaign->subject, ['body' => ..., 'tracking_url' => ...])` and send. An `UnexpectedResponseException` with a 5xx code updates the row to `bounced` with the message; any other failure (connection, 4xx) deletes the row, stops the run without setting `send_at` and makes the command fail, so the next run resumes the campaign. The command prints each result and a sent / bounced count. Creating the row first means a crash after sending never resends.
- At the end `send_at = now()` and `emails_count` = recipient rows of the campaign.
- `->withoutOverlapping()` on the schedule entry so a long run is not started twice.
- The `from` chosen in the campaign is kept: `GenericEmail` gets the user only for user addresses; for the company address it is built with `null` user, which already uses the company email and name in `envelope()`.

### Open tracking
- Route `GET /campaign/open/{recipient}` outside the auth group, named `campaign.open`, with the `signed` middleware; URLs come from `URL::signedRoute('campaign.open', $recipient)` without expiry, so old emails still count.
- `CampaignOpenController` increments `open_count` and sets `opened_at` when null with one `update` query, then returns a 43 byte transparent GIF with `Cache-Control: no-store`. The signed middleware rejects tampered URLs with 403 before the controller runs.
- `mail.generic` renders `<img src="{{ $tracking_url }}" width="1" height="1" alt="">` only when `tracking_url` is set, so other emails are unaffected.

### Validation and delete
- `CampaignSaveRequest`: `name` required string max 255, `subject` required max 255, `from` required email in the two allowed addresses (company email, user email), `body` required, `tags` nullable string, `schedule_send_date` nullable date, `schedule_send_time` nullable `H:i`.
- `DELETE /campaign/{id}` with `can('delete campaign')`; the list uses a small form with `@method('DELETE')` and a confirm. `delete campaign` added to `PermissionSeeder` for SuperAdmin, CompanyAdmin and Seller (same roles as create/update).
- The "Send" button is removed from the list.

### Report query
- `Campaign::getEmailReport(int $companyId)` with `withCount` on `recipients`: total, `bounced` (status bounced) and `opened` (`opened_at` not null), filtered by company and `whereNotNull('send_at')`, ordered by `send_at` desc. Delivered and rates are computed in PHP from the counts; totals from the summed counts.
- Rates rounded to one decimal; 0 when the divisor is 0.
- Campaigns sent before this change have no recipient rows: their sent figure comes from `emails_count` and opens/bounces show as 0. A short note under the table explains it.

## Risks / Trade-offs

- [Synchronous SMTP only catches rejections at send time] → Accepted for now; late bounces need provider webhooks (later change).
- [Mail clients that preload images (Apple Mail privacy) count as opens; clients that block images do not] → The report labels open rates as an estimate.
- [Long campaigns in the scheduler] → `withoutOverlapping()` plus the per recipient unique key; queued sending is a later change.
- [Public endpoint] → Signed URLs; it only updates the recipient in the URL and returns no data.

## Migration Plan

1. `php artisan migrate` (name with backfill, `campaign_recipient`, `schedule_at` with backfill).
2. `php artisan db:seed --class=PermissionSeeder` (`delete campaign`).

Rollback: rolling back the migrations drops `campaign_recipient` and `campaign.name`.
