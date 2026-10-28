<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Campaign;

use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Models\Lead;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CampaignCloneControllerTest extends TestCase
{
    #[Test]
    public function a_sent_campaign_is_duplicated_as_a_draft(): void
    {
        $campaign = Campaign::factory()->sent()->create([
            'company_id' => $this->user->company_id,
            'name' => 'Navidad 2026',
            'subject' => 'Feliz navidad',
            'body' => '<p>Hola {{ name }}</p>',
            'tags' => ['test'],
            'audience' => Campaign::AUDIENCE_TAGS,
            'schedule_send_time' => '10:20:00',
            'emails_count' => 8,
        ]);
        CampaignRecipient::factory()->create(['campaign_id' => $campaign->id]);

        $response = $this->post("/campaign/$campaign->id/clone");

        $copy = Campaign::where('id', '!=', $campaign->id)->sole();
        $response->assertRedirect("/campaign/update/$copy->id")->assertSessionHas('status', 'success');
        $this->assertSame('Copy of Navidad 2026', $copy->name);
        $this->assertSame('Feliz navidad', $copy->subject);
        $this->assertSame('<p>Hola {{ name }}</p>', $copy->body);
        $this->assertSame(['test'], $copy->tags);
        $this->assertSame(Campaign::AUDIENCE_TAGS, $copy->audience);
        $this->assertNull($copy->send_at);
        $this->assertNull($copy->emails_count);
        $this->assertNull($copy->schedule_send_date);
        $this->assertNull($copy->schedule_send_time);
        $this->assertNull($copy->schedule_at);
        $this->assertSame(0, $copy->recipients()->count());
        $this->get('/campaign')->assertSee('Copy of Navidad 2026');
    }

    #[Test]
    public function the_copy_is_not_sent_until_it_has_a_schedule_date(): void
    {
        Mail::fake();
        $campaign = Campaign::factory()->sent()->create(['company_id' => $this->user->company_id, 'from' => $this->user->email]);
        Lead::factory()->create(['company_id' => $this->user->company_id, 'email' => 'ana@example.com', 'opt_in' => true]);
        $this->post("/campaign/$campaign->id/clone");

        $this->artisan('crm:campaign:send')->assertSuccessful();

        Mail::assertNothingSent();
    }

    #[Test]
    public function a_campaign_of_another_company_cannot_be_duplicated(): void
    {
        $campaign = Campaign::factory()->create();

        $this->post("/campaign/$campaign->id/clone")->assertNotFound();

        $this->assertDatabaseCount('campaign', 1);
    }
}
