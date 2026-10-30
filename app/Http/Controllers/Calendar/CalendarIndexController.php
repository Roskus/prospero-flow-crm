<?php

declare(strict_types=1);

namespace App\Http\Controllers\Calendar;

use App\Http\Controllers\MainController;
use App\Models\Calendar;
use App\Models\CompanyHoliday;
use App\Models\TimeOff;
use App\Services\WorkHourService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
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

        $companyId = (int) Auth::user()->company_id;

        $events = Calendar::where('company_id', $companyId)
            ->whereBetween('start_date', [$startOfCalendar, $endOfCalendar])
            ->get();

        $holidays = CompanyHoliday::where('company_id', $companyId)
            ->whereBetween('date', [$startOfCalendar->toDateString(), $endOfCalendar->toDateString()])
            ->get()
            ->keyBy(fn (CompanyHoliday $holiday): string => $holiday->date->toDateString());

        $vacations = TimeOff::where('company_id', $companyId)
            ->whereIn('type', ['vacation', 'personal'])
            ->where('status', WorkHourService::TIME_OFF_APPROVED)
            ->whereDate('start_date', '<=', $endOfCalendar)
            ->whereDate('end_date', '>=', $startOfCalendar)
            ->with('user')
            ->orderBy('start_date')
            ->get();

        return view('calendar.calendar', compact('date', 'startOfCalendar', 'endOfCalendar', 'events', 'holidays', 'vacations'));
    }
}
