<?php

namespace App\Traits;

trait ICalendar
{
    private static string $EOL = "\r\n";

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
     * @return string
     */
    public function iCalendar(): string
    {
        $iCal = 'BEGIN:VCALENDAR'.self::$EOL;
        $iCal .= 'VERSION:2.0'.self::$EOL;

        return $iCal;
    }
}
