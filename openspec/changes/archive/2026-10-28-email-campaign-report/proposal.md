# Proposal

## Why

The email report is an empty page, and campaigns keep no data to fill it: only a total `emails_count` is stored, with no per recipient result, no opens and no failures. Campaigns are also identified only by their subject, so two campaigns with the same subject cannot be told apart. On top of that, the scheduled sender is broken: it builds `GenericEmail` with its arguments in the wrong order, and it picks every campaign of the day on every run (every 15 minutes) without checking whether it was already sent.

## What Changes

- **Campaign name**: campaigns get a required `name`, shown in the campaign list, the form and the report. Existing campaigns take their subject as name. The form is validated with a Form Request (name, subject, from, body, schedule).
- **Per recipient record** (new `campaign_recipient`): one row per email sent by a campaign with the lead, the email, the result (`sent` or `bounced`), the SMTP error, the send time and the first open time plus the open count.
- **Bounces**: an email rejected by the mail server at send time counts as bounced, with the error saved. Bounces reported later by the provider (webhooks of SES, Mailgun or Postmark) are left for a later change.
- **Open tracking**: each email carries a 1×1 image whose URL is signed and unique per recipient; loading it records the open. The URL works without login and reveals nothing.
- **Sender fixes**: only campaigns whose scheduled date and time (in the time zone of the user who saved them) have been reached and that were not sent yet, `GenericEmail` built with the right arguments, recipients of the campaign company only, a recipient never mailed twice by the same campaign, and the scheduled command does not overlap itself.
- **Delete**: the existing delete action gets a route and a new `delete campaign` permission; the dead "Send" button (it calls a function that does not exist) is removed, since campaigns are sent by their schedule.
- **Email report**: one row per sent campaign of the active company with name, sent date, emails sent, delivered, opens, bounced, open rate (opens / delivered) and bounce rate (bounced / sent), and a totals row.

## Capabilities

### New Capabilities

- `email-campaigns`: campaign name, scheduled sending, per recipient results, bounce detection at send time and open tracking.
- `email-campaign-report`: the email report figures, their rates, scoping and permissions.

### Modified Capabilities

None.

## Impact

- **Database**: `campaign.name` (backfilled from `subject`); `campaign.schedule_at` in UTC; new `campaign_recipient` table.
- **Models**: `Campaign` (`name`, `recipients()` relation, report query); new `CampaignRecipient` with factory.
- **Console**: `CampaignSender` rewritten; `withoutOverlapping()` on its schedule in `bootstrap/app.php`.
- **Mail**: `GenericEmail` gets the tracking pixel URL through its data; `mail.generic` renders it.
- **Controllers / requests**: `CampaignSaveRequest`; new public `CampaignOpenController` (signed route, image response); `ReportEmailController` with data; delete route.
- **Views**: `campaign/index`, `campaign/campaign`, `report/email`.
- **Permissions**: `delete campaign` in `PermissionSeeder` (existing installs run the seeder).
- **Privacy**: open tracking stores only a time and a counter per recipient; some mail clients preload images (counted as opens) or block them (opens not counted), so the open rate is an estimate.
