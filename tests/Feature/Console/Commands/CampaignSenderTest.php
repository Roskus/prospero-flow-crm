<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands;

use App\Mail\GenericEmail;
use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Models\Lead;
use Illuminate\Mail\PendingMail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\UnexpectedResponseException;
use Tests\TestCase;

class CampaignSenderTest extends TestCase
{
    /**
     * @var list<string>
     */
    private array $deliveredEmails = [];

    /**
     * @var array<string, UnexpectedResponseException>
     */
    private array $rejections = [];

    private bool $mailServerIsUnreachable = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(Carbon::parse('2026-10-15 10:00'));
    }

    #[Test]
    public function a_due_campaign_is_sent_to_the_opted_in_leads_of_its_company(): void
    {
        Mail::fake();
        $campaign = $this->campaign(['schedule_send_date' => '2026-10-15', 'schedule_send_time' => '09:30:00']);
        $this->lead('ana@example.com');
        $this->lead('bruno@example.com');
        $this->lead('carla@example.com');
        $this->lead('optout@example.com', ['opt_in' => false]);
        $this->lead(null);
        Lead::factory()->create(['email' => 'other@example.com', 'opt_in' => true]);

        $this->artisan('crm:campaign:send')->assertSuccessful();

        Mail::assertSentCount(3);
        Mail::assertSent(GenericEmail::class, fn (GenericEmail $email): bool => $email->hasTo('ana@example.com'));
        Mail::assertNotSent(GenericEmail::class, fn (GenericEmail $email): bool => $email->hasTo('optout@example.com') || $email->hasTo('other@example.com'));
        $campaign->refresh();
        $this->assertNotNull($campaign->send_at);
        $this->assertSame(3, $campaign->emails_count);
        $this->assertSame(3, $campaign->recipients()->where('status', CampaignRecipient::STATUS_SENT)->count());
    }

    #[Test]
    public function a_campaign_scheduled_for_later_is_not_sent(): void
    {
        Mail::fake();
        $later = $this->campaign(['schedule_send_date' => '2026-10-15', 'schedule_send_time' => '10:30:00']);
        $tomorrow = $this->campaign(['schedule_send_date' => '2026-10-16']);
        $draft = $this->campaign(['schedule_send_date' => null]);
        $this->lead('ana@example.com');

        $this->artisan('crm:campaign:send')->assertSuccessful();

        Mail::assertNothingSent();
        $this->assertNull($later->fresh()->send_at);
        $this->assertNull($tomorrow->fresh()->send_at);
        $this->assertNull($draft->fresh()->send_at);
    }

    #[Test]
    public function a_campaign_of_a_past_day_without_time_is_sent(): void
    {
        Mail::fake();
        $campaign = $this->campaign(['schedule_send_date' => '2026-10-14']);
        $this->lead('ana@example.com');

        $this->artisan('crm:campaign:send')->assertSuccessful();

        Mail::assertSentCount(1);
        $this->assertNotNull($campaign->fresh()->send_at);
    }

    #[Test]
    public function the_schedule_is_read_in_the_time_zone_of_the_user_who_saved_it(): void
    {
        Mail::fake();
        $this->user->update(['timezone' => 'Europe/Madrid']);
        $this->lead('ana@example.com');
        $this->post('/campaign/save', [
            'name' => 'Madrid campaign',
            'subject' => 'Hello',
            'from' => $this->user->email,
            'body' => '<p>Hello</p>',
            'schedule_send_date' => '2026-10-15',
            'schedule_send_time' => '11:30',
        ])->assertRedirect('/campaign');
        $campaign = Campaign::firstWhere('name', 'Madrid campaign');
        $this->assertSame('2026-10-15 09:30:00', $campaign->schedule_at->toDateTimeString());

        $this->travelTo(Carbon::parse('2026-10-15 09:29'));
        $this->artisan('crm:campaign:send')->assertSuccessful();
        Mail::assertNothingSent();

        $this->travelTo(Carbon::parse('2026-10-15 09:31'));
        $this->artisan('crm:campaign:send')->assertSuccessful();
        Mail::assertSentCount(1);
    }

    #[Test]
    public function a_sent_campaign_is_not_sent_again(): void
    {
        Mail::fake();
        $this->campaign(['schedule_send_date' => '2026-10-15']);
        $this->lead('ana@example.com');

        $this->artisan('crm:campaign:send')->assertSuccessful();
        $this->artisan('crm:campaign:send')->assertSuccessful();

        Mail::assertSentCount(1);
    }

    #[Test]
    public function a_stopped_run_resumes_without_mailing_anyone_twice(): void
    {
        Mail::fake();
        $campaign = $this->campaign(['schedule_send_date' => '2026-10-15']);
        $ana = $this->lead('ana@example.com');
        $this->lead('bruno@example.com');
        CampaignRecipient::factory()->create(['campaign_id' => $campaign->id, 'lead_id' => $ana->id, 'email' => 'ana@example.com']);

        $this->artisan('crm:campaign:send')->assertSuccessful();

        Mail::assertSentCount(1);
        Mail::assertSent(GenericEmail::class, fn (GenericEmail $email): bool => $email->hasTo('bruno@example.com'));
        $this->assertSame(2, $campaign->fresh()->emails_count);
    }

    #[Test]
    public function an_email_rejected_by_the_mail_server_is_bounced_and_the_rest_are_sent(): void
    {
        $campaign = $this->campaign(['schedule_send_date' => '2026-10-15']);
        $this->lead('ana@example.com');
        $this->lead('wrong@example.com');
        $this->lead('carla@example.com');
        $this->mailServerRejects('wrong@example.com', 550, '550 5.1.1 Mailbox does not exist');

        $this->artisan('crm:campaign:send')
            ->expectsOutput('Bounced: wrong@example.com')
            ->expectsOutput('Sent: 2, bounced: 1')
            ->assertSuccessful();

        $this->assertEqualsCanonicalizing(['ana@example.com', 'carla@example.com'], $this->deliveredEmails);
        $bounced = $campaign->recipients()->where('email', 'wrong@example.com')->first();
        $this->assertSame(CampaignRecipient::STATUS_BOUNCED, $bounced->status);
        $this->assertStringContainsString('Mailbox does not exist', $bounced->error);
        $this->assertSame(2, $campaign->recipients()->where('status', CampaignRecipient::STATUS_SENT)->count());
        $this->assertSame(3, $campaign->fresh()->emails_count);
    }

    #[Test]
    public function the_campaign_stays_pending_when_the_mail_server_cannot_be_reached(): void
    {
        $campaign = $this->campaign(['schedule_send_date' => '2026-10-15']);
        $this->lead('ana@example.com');
        $this->lead('bruno@example.com');
        $this->mailServerIsUnreachable();

        $this->artisan('crm:campaign:send')
            ->expectsOutputToContain('Campaign stopped, it will be resumed on the next run')
            ->assertFailed();

        $this->assertNull($campaign->fresh()->send_at);
        $this->assertSame(0, $campaign->recipients()->count());
    }

    #[Test]
    public function a_temporary_rejection_stops_the_campaign_and_the_next_run_resumes_it(): void
    {
        $campaign = $this->campaign(['schedule_send_date' => '2026-10-15']);
        $this->lead('ana@example.com');
        $this->lead('bruno@example.com');
        $this->mailServerRejects('bruno@example.com', 421, '421 Service not available');

        $this->artisan('crm:campaign:send')->assertFailed();

        $this->assertNull($campaign->fresh()->send_at);
        $this->assertSame(['ana@example.com'], $campaign->recipients()->pluck('email')->all());

        $this->mailServerAcceptsAgain('bruno@example.com');
        $this->artisan('crm:campaign:send')->expectsOutput('Sent: 2, bounced: 0')->assertSuccessful();

        $this->assertSame(['ana@example.com', 'bruno@example.com'], $this->deliveredEmails);
        $this->assertSame(2, $campaign->fresh()->emails_count);
    }

    #[Test]
    public function the_name_variables_are_replaced_with_the_recipient_values(): void
    {
        Mail::fake();
        $this->campaign([
            'schedule_send_date' => '2026-10-15',
            'subject' => 'News for {{ name }}',
            'body' => '<p>Hola {{name}}! {{ business_name }} {{ password }} {{ phpinfo() }}</p><p>Old: {{$prospect-&gt;first_name}}</p>',
        ]);
        $this->lead('ana@example.com', ['name' => 'Ana <b>López</b>', 'business_name' => 'Flores & Co']);

        $this->artisan('crm:campaign:send')->assertSuccessful();

        Mail::assertSent(GenericEmail::class, function (GenericEmail $email): bool {
            $html = $email->render();

            return $email->subject === 'News for Ana <b>López</b>'
                && str_contains($html, 'Hola Ana &lt;b&gt;López&lt;/b&gt;!')
                && str_contains($html, 'Flores &amp; Co')
                && str_contains($html, 'Old: Ana &lt;b&gt;López&lt;/b&gt;')
                && str_contains($html, '{{ password }}')
                && str_contains($html, '{{ phpinfo() }}');
        });
    }

    #[Test]
    public function every_email_carries_its_own_signed_tracking_image(): void
    {
        Mail::fake();
        $campaign = $this->campaign(['schedule_send_date' => '2026-10-15']);
        $this->lead('ana@example.com');

        $this->artisan('crm:campaign:send')->assertSuccessful();

        $recipient = $campaign->recipients()->first();
        Mail::assertSent(GenericEmail::class, function (GenericEmail $email) use ($recipient): bool {
            $html = $email->render();

            return str_contains($html, url("/campaign/open/$recipient->id").'?signature=')
                && str_contains($html, 'width="1" height="1"');
        });
    }

    private function mailServerIsUnreachable(): void
    {
        $this->mailServerIsUnreachable = true;
        $this->fakeMailServer();
    }

    private function mailServerRejects(string $email, int $code, string $message): void
    {
        $this->rejections[$email] = new UnexpectedResponseException($message, $code);
        $this->fakeMailServer();
    }

    private function mailServerAcceptsAgain(string $email): void
    {
        unset($this->rejections[$email]);
    }

    /**
     * Replaces the mailer with a fake SMTP server driven by $mailServerIsUnreachable and $rejections; accepted emails go to $deliveredEmails.
     */
    private function fakeMailServer(): void
    {
        Mail::shouldReceive('to')->andReturnUsing(function (string $email): PendingMail {
            $pendingMail = Mockery::mock(PendingMail::class);
            $pendingMail->shouldReceive('send')->andReturnUsing(function () use ($email): void {
                $this->deliver($email);
            });

            return $pendingMail;
        });
    }

    private function deliver(string $email): void
    {
        if ($this->mailServerIsUnreachable) {
            throw new TransportException('Connection could not be established with host "mail:1025"');
        }

        if (isset($this->rejections[$email])) {
            throw $this->rejections[$email];
        }

        $this->deliveredEmails[] = $email;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function campaign(array $attributes): Campaign
    {
        $attributes += ['schedule_send_time' => null];

        return Campaign::factory()->create([
            'company_id' => $this->user->company_id,
            'from' => $this->user->email,
            'schedule_at' => Campaign::scheduleMoment($attributes['schedule_send_date'], $attributes['schedule_send_time'], 'UTC'),
            ...$attributes,
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function lead(?string $email, array $attributes = []): Lead
    {
        return Lead::factory()->create([
            'company_id' => $this->user->company_id,
            'email' => $email,
            'opt_in' => true,
            ...$attributes,
        ]);
    }
}
