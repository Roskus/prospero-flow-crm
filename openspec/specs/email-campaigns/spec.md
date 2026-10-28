# email-campaigns Specification

## Purpose
Lets a company send scheduled email campaigns to its opted-in leads and keep, for every email sent, whether it was accepted or bounced and whether it was opened.

## Requirements

### Requirement: Campaign name
Every campaign SHALL have a name of at most 255 characters, required when creating or updating it, together with a subject, a from address of the company or of the user, and a body. The campaign list SHALL show the name. Campaigns that existed before this change SHALL take their subject as name.

#### Scenario: Saving without a name
- **WHEN** a user saves a campaign without a name
- **THEN** the campaign is not saved and a validation error is shown for the name

#### Scenario: Existing campaigns
- **WHEN** the change is deployed on a database with campaigns
- **THEN** each campaign has its subject as name

#### Scenario: Campaign of another company
- **WHEN** a user opens, saves or deletes a campaign of another company by its id
- **THEN** the request returns not found

### Requirement: Scheduled sending
A campaign SHALL be sent once, when its scheduled date and time have been reached (a campaign without a time is sent from the start of its scheduled date). The scheduled date and time SHALL be read in the time zone of the user who saved the campaign (the application time zone when the user has none). The campaign SHALL be sent to the contacts of the campaign company with an email address and opt in, among its leads, its customers or both, as chosen in the campaign (leads by default, also for campaigns created before this option): all of them when the campaign is sent to everyone, or only those with any of the campaign tags, compared without case, when it is sent by tags. A campaign sent by tags SHALL have at least one tag. An email address present in several contacts SHALL receive the campaign once. A campaign already sent SHALL NOT be sent again, and a recipient SHALL NOT receive the same campaign twice, even if a previous run stopped half way. When the run ends, the campaign SHALL store its send time and the number of emails sent.

#### Scenario: Due campaign
- **WHEN** the sender runs after the scheduled date and time of an unsent campaign with three opted-in leads
- **THEN** three emails are sent and the campaign is marked as sent with an emails count of 3

#### Scenario: Scheduled in the user time zone
- **WHEN** a user in Europe/Madrid schedules a campaign for 11:30 and the sender runs at 11:31 Madrid time (09:31 UTC)
- **THEN** the campaign is sent, and it is not sent at 11:29 Madrid time

#### Scenario: Not due yet
- **WHEN** the sender runs before the scheduled time of a campaign
- **THEN** no email of that campaign is sent

#### Scenario: Already sent
- **WHEN** the sender runs again after a campaign was sent
- **THEN** no email of that campaign is sent again

#### Scenario: Sent by tags
- **WHEN** a campaign sent by tags has the tag "Test" and leads are tagged "test", "testing" and "other"
- **THEN** only the lead tagged "test" receives it

#### Scenario: Leads and customers
- **WHEN** a campaign for leads and customers is sent and a lead and a customer share the same email address
- **THEN** that address receives one email, and every other opted-in lead and customer of the company receives it too

#### Scenario: Sent to all leads
- **WHEN** a campaign sent to all leads has tags
- **THEN** every opted-in lead of the company receives it, whatever its tags

#### Scenario: Leads of other companies or without opt in
- **WHEN** a campaign is sent
- **THEN** leads of other companies and leads without opt in or without email receive nothing

### Requirement: Personalized content
The subject and the body of each email SHALL replace `{{ name }}` with the recipient name and `{{ business_name }}` with the recipient business name, with or without spaces inside the braces. Campaigns written with the older `{{ $prospect->first_name }}`, `{{ $prospect->name }}` or `{{ $prospect->business_name }}` forms (also when the editor stored `->` as `-&gt;`) SHALL be replaced the same way. Values SHALL be escaped in the body. The content SHALL never be evaluated as a template; any other `{{ ... }}` text SHALL be sent unchanged.

#### Scenario: Greeting by name
- **WHEN** a campaign body contains `Hola {{ name }}!` and is sent to the lead Ana
- **THEN** the email body contains `Hola Ana!`

#### Scenario: Older variable form
- **WHEN** a campaign body contains `{{$prospect-&gt;first_name}}` and is sent to the lead Ana
- **THEN** the email body contains `Ana`

#### Scenario: Unknown variables and code
- **WHEN** a campaign body contains `{{ password }}` or `{{ phpinfo() }}`
- **THEN** that text is sent unchanged and nothing is executed

### Requirement: Per recipient result
Each email of a campaign SHALL be recorded with its recipient email, its lead, its send time and its result. An email accepted by the mail server SHALL be `sent`; an email permanently rejected by the mail server when sending (5xx reply) SHALL be `bounced`, keeping the error message, and SHALL NOT stop the rest of the campaign. When the mail server cannot be reached or answers with a temporary error, the run SHALL stop without recording that email, the campaign SHALL stay pending and the next run SHALL resume it with the recipients not mailed yet. The sender SHALL print each result and a final count of sent and bounced emails. Bounces reported later by the mail provider are out of scope.

#### Scenario: Mail server not reachable
- **WHEN** the sender runs and the mail server cannot be reached
- **THEN** no recipient is recorded, the campaign is not marked as sent and the command reports the error and fails

#### Scenario: Mail server rejects one recipient
- **WHEN** the mail server rejects one of three recipients
- **THEN** two recipients are `sent`, one is `bounced` with the error, and the other two emails are delivered

### Requirement: Open tracking
Each campaign email SHALL include a tracking image whose address is unique to the recipient and signed, so it cannot be guessed or changed. Loading the address SHALL work without login, SHALL return a 1×1 transparent image, SHALL store the first open time and SHALL increase the open count of that recipient. An address with a missing or wrong signature SHALL record nothing.

#### Scenario: First open
- **WHEN** the recipient mail client loads the tracking image
- **THEN** the recipient gets its first open time and an open count of 1, and an image is returned

#### Scenario: Opening again
- **WHEN** the tracking image of an opened recipient is loaded again
- **THEN** the open count increases and the first open time does not change

#### Scenario: Tampered address
- **WHEN** the tracking address is loaded with another recipient id or a wrong signature
- **THEN** nothing is recorded and the request is rejected

### Requirement: Duplicating a campaign
Users with the `create campaign` permission SHALL be able to duplicate a campaign of their company. The copy SHALL keep the subject, from address, body, tags and audience, SHALL be named "Copy of" plus the original name, and SHALL be a draft: no schedule date or time, no send time, no emails count and no recipients. A draft SHALL NOT be sent until a schedule date is set.

#### Scenario: Duplicating a sent campaign
- **WHEN** a user duplicates a sent campaign
- **THEN** a draft copy is created with the same content and the user is taken to its form

#### Scenario: The copy is not sent
- **WHEN** the sender runs after a campaign was duplicated
- **THEN** the copy is not sent

### Requirement: Deleting a campaign
Users with the `delete campaign` permission SHALL be able to delete a campaign of their company after a confirmation; the campaign stops showing in the campaign list and in the email report. Users without the permission SHALL get forbidden.

#### Scenario: Deleting a campaign
- **WHEN** a user with `delete campaign` confirms the deletion of a campaign
- **THEN** the campaign no longer shows in the campaign list

#### Scenario: Without permission
- **WHEN** a user without `delete campaign` tries to delete a campaign
- **THEN** the request is forbidden and the campaign is kept
