<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Dashboard;

use App\Models\Calendar;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DashboardIndexControllerTest extends TestCase
{
    #[Test]
    public function it_shows_calendar_event_start_time_on_the_dashboard(): void
    {
        $this->user->update(['timezone' => 'UTC']);

        Calendar::factory()->create([
            'company_id' => $this->user->company_id,
            'user_id' => $this->user->id,
            'title' => 'Evento dashboard',
            'start_date' => now()->startOfMonth()->addDay()->setTime(20, 0, 0)->toDateTimeString(),
            'end_date' => now()->startOfMonth()->addDay()->setTime(21, 0, 0)->toDateTimeString(),
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSeeText('20:00 - Evento dashboard');
    }
}
