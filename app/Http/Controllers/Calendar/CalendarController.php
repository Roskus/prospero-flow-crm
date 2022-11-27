<?php

namespace App\Http\Controllers\Calendar;

use App\Http\Controllers\MainController;
use App\Models\Calendar;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends MainController
{
    /**
     * @param  Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $date = empty($request->date) ? Carbon::now() : Carbon::createFromDate($request->date);
        $startOfCalendar = $date->copy()->firstOfMonth()->startOfWeek(Carbon::MONDAY);
        $endOfCalendar = $date->copy()->lastOfMonth()->endOfWeek(Carbon::SUNDAY);

        $events = Calendar::whereUserId(auth()->id())
                            ->where('start_date', '>=', $startOfCalendar)
                            ->where('start_date', '<=', $endOfCalendar)
                            ->get();

        return view('calendar.calendar', compact('date', 'startOfCalendar', 'endOfCalendar', 'events'));
    }
}
