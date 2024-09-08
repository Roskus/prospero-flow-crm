<?php

namespace Tests\Feature\Mail;

use App\Mail\EventCalendarEmail;
use App\Models\Calendar;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EventCalendarEmailTest extends TestCase
{
    #[Test]
    public function it_can_create_event_calendar_email(): void
    {
        Mail::fake();

        $calendar = Calendar::factory()->make();
        $user = $this->user;

        Mail::to($user)->send(new EventCalendarEmail($calendar));

        Mail::assertSent(EventCalendarEmail::class, function (EventCalendarEmail $mail) use ($user) {
            return $user->name === $mail->to[0]['name'] &&
                $mail->to[0]['name'] !== null &&
                $user->email === $mail->to[0]['address'] &&
                $mail->to[0]['address'] !== null;
        });
    }
}
