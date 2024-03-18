<?php

declare(strict_types=1);

namespace App\Http\Controllers\Calendar;

use App\Http\Controllers\Controller;
use App\Models\Calendar as CalendarModel;
use DateTime;
use Illuminate\Support\Str;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;

class CalendarExportController extends Controller
{
    public function exportICal(int $id): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        // Buscar el evento de calendario por su ID
        $calendarEvent = CalendarModel::find($id);

        // Verificar si el evento existe
        if (! $calendarEvent) {
            abort(404, 'Evento de calendario no encontrado.');
        }

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
