<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Calendar;

use App\Models\Calendar;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CalendarExportControllerTest extends TestCase
{
    #[Test]
    public function it_blocks_unauthenticated_calendar_export(): void
    {
        $event = Calendar::factory()->create(['user_id' => $this->user->id]);

        auth()->guard('web')->logout();

        $response = $this->get("/calendar/{$event->id}/export");

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function it_blocks_cross_user_calendar_export(): void
    {
        $otherUser = User::factory()->create();
        $event = Calendar::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->get("/calendar/{$event->id}/export");

        $response->assertNotFound();
    }

    #[Test]
    public function it_can_export_own_calendar_event(): void
    {
        $event = Calendar::factory()->create(['user_id' => $this->user->id]);

        $response = $this->get("/calendar/{$event->id}/export");

        $response->assertOk();
        $response->assertDownload();
    }
}
