<?php

declare(strict_types=1);

namespace App\Http\Controllers\Calendar;

use App\Http\Controllers\MainController;
use App\Models\Calendar;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class CalendarIndexController extends MainController
{
    /**
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        $date = empty($request->date) ? Carbon::now() : Carbon::createFromDate($request->date);
        $startOfCalendar = $date->copy()->firstOfMonth()->startOfWeek(Carbon::MONDAY);
        $endOfCalendar = $date->copy()->lastOfMonth()->endOfWeek(Carbon::SUNDAY);

        $events = Calendar::whereUserId(auth()->id())
            ->whereBetween('start_date', [$startOfCalendar, $endOfCalendar])
            ->get();

        return view('calendar.calendar', compact('date', 'startOfCalendar', 'endOfCalendar', 'events'));
    }
}
