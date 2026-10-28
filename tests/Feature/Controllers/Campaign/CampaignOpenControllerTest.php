<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Campaign;

use App\Models\CampaignRecipient;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CampaignOpenControllerTest extends TestCase
{
    #[Test]
    public function the_first_open_is_recorded_without_login(): void
    {
        $recipient = CampaignRecipient::factory()->create();
        Auth::logout();

        $this->get($this->trackingUrl($recipient))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/gif');

        $recipient->refresh();
        $this->assertSame(1, $recipient->open_count);
        $this->assertSame(now()->toDateTimeString(), $recipient->opened_at->toDateTimeString());
    }

    #[Test]
    public function opening_again_counts_but_keeps_the_first_open_time(): void
    {
        $recipient = CampaignRecipient::factory()->create();
        $this->get($this->trackingUrl($recipient))->assertOk();
        $firstOpen = $recipient->fresh()->opened_at;

        $this->travelTo(Carbon::now()->addHour());
        $this->get($this->trackingUrl($recipient))->assertOk();

        $recipient->refresh();
        $this->assertSame(2, $recipient->open_count);
        $this->assertTrue($recipient->opened_at->equalTo($firstOpen));
    }

    #[Test]
    public function a_tampered_address_records_nothing(): void
    {
        $recipient = CampaignRecipient::factory()->create();
        $other = CampaignRecipient::factory()->create();
        $signature = parse_url($this->trackingUrl($recipient), PHP_URL_QUERY);

        $this->get("/campaign/open/$other->id?$signature")->assertForbidden();
        $this->get("/campaign/open/$recipient->id")->assertForbidden();
        $this->get("/campaign/open/$recipient->id?signature=wrong")->assertForbidden();

        $this->assertSame(0, $recipient->fresh()->open_count);
        $this->assertSame(0, $other->fresh()->open_count);
    }

    private function trackingUrl(CampaignRecipient $recipient): string
    {
        return URL::signedRoute('campaign.open', ['recipient' => $recipient->id]);
    }
}
