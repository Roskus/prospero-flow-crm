<?php

declare(strict_types=1);

namespace App\Http\Controllers\Calendar;

use App\Http\Controllers\MainController;
use App\Models\Calendar as CalendarModel;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CalendarExportController extends MainController
{
    public function exportICal(int $id): BinaryFileResponse
    {
        $calendarEvent = CalendarModel::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Crear el objeto Calendar
        $calendar = Calendar::create($calendarEvent->title);

        // Crear el objeto Event con los detalles del evento de calendario
        $event = Event::create($calendarEvent->title)
            ->startsAt(new DateTime($calendarEvent->start_date))
            ->endsAt(new DateTime($calendarEvent->end_date))
            ->description((string) $calendarEvent->description); // Puedes agregar más propiedades según necesites

        // Agregar el organizador si existe el user_id
        if ($calendarEvent->user_id) {
            // Obtener información del organizador según el user_id
            $organizerEmail = $calendarEvent->organizer->email;
            $organizerName = $calendarEvent->organizer->getFullName();

            $event->organizer($organizerEmail, $organizerName);
        }

        // Agregar el evento al calendario
        $calendar->event($event);

        // Generar el archivo iCalendar y obtener su contenido
        $iCalContent = $calendar->get();

        // Guardar el contenido en un archivo temporal
        $tempFilePath = tempnam(sys_get_temp_dir(), 'calendar');
        file_put_contents($tempFilePath, $iCalContent);

        // Descargar el archivo iCalendar
        return response()
            ->download($tempFilePath, Str::slug($calendarEvent->title).'.ics')
            ->deleteFileAfterSend(true);
    }
}
