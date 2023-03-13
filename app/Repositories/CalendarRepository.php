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
            $calendar = new Calendar();
        } else {
            $calendar = Calendar::find($data['id']);
        }

        $calendar->company_id = Auth::user()->company_id;
        $calendar->user_id = Auth::user()->id;
        $calendar->start_date = $data['date'].' '.$data['start_time'];
        $calendar->end_date = $data['date'].' '.$data['end_time'];
        $calendar->is_all_day = $data['is_all_day'] ?? false;
        $calendar->title = $data['title'];
        $calendar->description = $data['description'];
        $calendar->guests = $data['guest_list'];
        $calendar->meeting = $data['meeting'];
        $calendar->address = $data['address'];
        $calendar->latitude = $data['latitude'];
        $calendar->longitude = $data['longitude'];
        $calendar->created_at = now();
        $calendar->save();

        return $calendar;
    }
}
