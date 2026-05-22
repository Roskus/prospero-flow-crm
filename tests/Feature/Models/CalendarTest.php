<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Calendar;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    #[Test]
    public function it_can_use_timezones()
    {
        $this->assertEquals(config('app.timezone'), $this->user->timezone);

        // ### UTC
        $this->user->timezone = 'UTC';
        $this->user->save();

        $this->post('calendar/event/save', [
            'title' => fake()->title(),
            'date' => '2022-12-15',
            'start_time' => '20:00',
            'end_time' => '21:00',
        ]);
        $event = DB::table('calendar')->orderBy('id')->first();
        $this->assertEquals('2022-12-15 20:00:00', $event->start_date);
        $this->assertEquals('2022-12-15 21:00:00', $event->end_date);

        $event = Calendar::orderBy('id')->first();
        $this->assertEquals('2022-12-15 20:00:00', $event->start_date);
        $this->assertEquals('2022-12-15 21:00:00', $event->end_date);

        // ### Europe/Madrid
        $this->user->timezone = 'Europe/Madrid';
        $this->user->save();

        $this->post('calendar/event/save', [
            'title' => fake()->title(),
            'date' => '2022-12-15',
            'start_time' => '20:00',
            'end_time' => '21:00',
        ]);
        $event = DB::table('calendar')->orderBy('id', 'desc')->first();
        $this->assertEquals('2022-12-15 19:00:00', $event->start_date);
        $this->assertEquals('2022-12-15 20:00:00', $event->end_date);

        $event = Calendar::orderBy('id', 'desc')->first();
        $this->assertEquals('2022-12-15 20:00:00', $event->start_date);
        $this->assertEquals('2022-12-15 21:00:00', $event->end_date);
    }
}
