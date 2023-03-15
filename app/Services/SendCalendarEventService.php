<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\EventCalendarEmail;
use App\Models\Calendar;
use Illuminate\Support\Facades\Mail;

class SendCalendarEventService
{
    public function send(Calendar $calendar)
    {
        if (count($calendar['guests']) > 0) {
            foreach ($calendar['guests'] as $guest) {
                if (filter_var($guest, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($guest)->send(new EventCalendarEmail($calendar));
                }
            }
        }
    }
}
