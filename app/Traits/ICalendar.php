<?php

declare(strict_types=1);

namespace App\Traits;

use OpenApi\Attributes as OAT;

trait ICalendar
{
    private static string $EOL = "\r\n";

    #[OAT\Property(type: 'string', format: 'date', example: '2022-01-27')]
    protected ?string $start_date;

    #[OAT\Property(type: 'string', example: 'My awesome event')]
    protected ?string $title = '';

    /**
     *  https://www.rfc-editor.org/rfc/rfc6868
     *  BEGIN:VCALENDAR
     *  VERSION:2.0
     *  CALSCALE:GREGORIAN
     *  BEGIN:VEVENT
     *  SUMMARY:Access-A-Ride Pickup
     *  DTSTART;TZID=America/New_York:20130802T103400
     *  DTEND;TZID=America/New_York:20130802T110400
     *  LOCATION:1000 Broadway Ave.\, Brooklyn
     *  DESCRIPTION: Access-A-Ride to 900 Jay St.\, Brooklyn
     *  STATUS:CONFIRMED
     *  SEQUENCE:3
     *  BEGIN:VALARM
     *  TRIGGER:-PT10M
     *  DESCRIPTION:Pickup Reminder
     *  ACTION:DISPLAY
     *  END:VALARM
     *  END:VEVENT
     *
     *  BEGIN:VEVENT
     *  SUMMARY:Access-A-Ride Pickup
     *  DTSTART;TZID=America/New_York:20130802T200000
     *  DTEND;TZID=America/New_York:20130802T203000
     *  LOCATION:900 Jay St.\, Brooklyn
     *  DESCRIPTION: Access-A-Ride to 1000 Broadway Ave.\, Brooklyn
     *  STATUS:CONFIRMED
     *  SEQUENCE:3
     *  END:VEVENT
     *  END:VCALENDAR
     */
    public function iCalendar(): string
    {
        $iCal = 'BEGIN:VCALENDAR'.self::$EOL;
        $iCal .= 'VERSION:2.0'.self::$EOL;
        $iCal .= 'CALSCALE:GREGORIAN'.self::$EOL;
        $iCal .= 'BEGIN:VEVENT'.self::$EOL;
        $iCal .= 'SUMMARY:'.$this->title.self::$EOL;
        // $iCal .= 'DTSTART;TZID='.self::$EOL;
        // $iCal .= 'DTEND;TZID='.self::$EOL;
        // $iCal .= 'LOCATION:'.self::$EOL;
        // $iCal .= 'DESCRIPTION:'.self::$EOL;

        $iCal .= 'END:VEVENT'.self::$EOL;
        $iCal .= 'END:VCALENDAR'.self::$EOL;

        return $iCal;
    }
}
