<?php

namespace Tests\Feature\Models;

use App\Models\Calendar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_use_timezones()
    {
        $user = $this->signIn();

        $this->assertEquals(config('app.timezone'), $user->timezone);

        // ### UTC
        $user->timezone = 'UTC';
        $user->save();

        $this->actingAs($user);
        $this->post('calendar/event/save', [
            'title' => fake()->title(),
            'date' => '2022-12-15',
            'start_time' => '20:00',
            'end_time' => '21:00',
        ]);
        $event = DB::table('calendar')->find(1);
        $this->assertEquals('2022-12-15 20:00:00', $event->start_date);
        $this->assertEquals('2022-12-15 21:00:00', $event->end_date);

        $event = Calendar::find(1);
        $this->assertEquals('2022-12-15 20:00:00', $event->start_date);
        $this->assertEquals('2022-12-15 21:00:00', $event->end_date);

        // ### Europe/Madrid
        $user->timezone = 'Europe/Madrid';
        $user->save();
        $this->actingAs($user);
        $this->post('calendar/event/save', [
            'title' => fake()->title(),
            'date' => '2022-12-15',
            'start_time' => '20:00',
            'end_time' => '21:00',
        ]);
        $event = DB::table('calendar')->find(2);
        $this->assertEquals('2022-12-15 19:00:00', $event->start_date);
        $this->assertEquals('2022-12-15 20:00:00', $event->end_date);

        $event = Calendar::find(2);
        $this->assertEquals('2022-12-15 20:00:00', $event->start_date);
        $this->assertEquals('2022-12-15 21:00:00', $event->end_date);
    }
}
