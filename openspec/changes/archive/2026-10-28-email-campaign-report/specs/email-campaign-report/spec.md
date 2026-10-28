# Spec Delta

## Purpose

Shows how each sent email campaign of the company performed: how many emails were sent, delivered, opened and bounced, and the open and bounce rates.

## ADDED Requirements

### Requirement: Campaign results
The email report SHALL list the sent campaigns of the active company, most recent first, with: name, send date, emails sent, delivered (sent minus bounced), opens (recipients that opened at least once), bounced, open rate (opens / delivered) and bounce rate (bounced / emails sent). Rates SHALL be percentages with one decimal and SHALL show 0 when the divisor is 0. A totals row SHALL show the sums and the rates of the totals. Campaigns not sent yet and deleted campaigns SHALL NOT be listed.

#### Scenario: Campaign with opens and bounces
- **WHEN** a campaign was sent to 10 recipients, 2 bounced and 4 opened
- **THEN** its row shows 10 sent, 8 delivered, 4 opens, 2 bounced, open rate 50.0 % and bounce rate 20.0 %

#### Scenario: Recipient opened several times
- **WHEN** a recipient opened the email three times
- **THEN** it counts as one open

#### Scenario: No campaigns sent
- **WHEN** the company has no sent campaigns
- **THEN** the report shows an empty state message

#### Scenario: Other companies
- **WHEN** another company has sent campaigns
- **THEN** they are not listed and not included in the totals

### Requirement: Report access
The email report SHALL be available to users with the `read report` permission and listed in the Communication group of the reports screen. Users without it SHALL get forbidden. All labels SHALL be translated to English and Spanish.

#### Scenario: Without permission
- **WHEN** a user without `read report` opens the email report
- **THEN** the request is forbidden
