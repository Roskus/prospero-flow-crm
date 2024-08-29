<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Calendar;
use Illuminate\Support\Facades\Auth;

class CalendarRepository
{
    public function save(array $data): Calendar
    {
        if (empty($data['id'])) {
            $calendar = new Calendar;
            $calendar->created_at = now();
        } else {
            $calendar = Calendar::find($data['id']);
        }

        $calendar->company_id = Auth::user()->company_id;
        $calendar->user_id = Auth::user()->id;
        $calendar->start_date = $data['date'].' '.$data['start_time'];
        $calendar->end_date = $data['date'].' '.$data['end_time'];
        $calendar->is_all_day = $data['is_all_day'] ?? false;
        $calendar->title = $data['title'];
        $calendar->description = (! empty($data['description'])) ? $data['description'] : null;
        $calendar->guests = (! empty($data['guest_list'])) ? $data['guest_list'] : null;
        $calendar->meeting = (! empty($data['meeting'])) ? $data['meeting'] : null;
        $calendar->address = (! empty($data['address'])) ? $data['address'] : null;
        $calendar->latitude = (! empty($data['latitude'])) ? $data['latitude'] : null;
        $calendar->longitude = (! empty($data['longitude'])) ? $data['longitude'] : null;

        $calendar->updated_at = now();
        $calendar->save();

        return $calendar;
    }
}
