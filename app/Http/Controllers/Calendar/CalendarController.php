<?php

namespace App\Http\Controllers\Calendar;

use App\Http\Controllers\MainController;
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
        $today = Carbon::now();
        $today_day_number = $today->day;
        $day_of_week = Carbon::now()->dayOfWeek;
        $week = [];
        switch ($day_of_week) {
            case 0:
                $week[0] = $today_day_number;
                $week[1] = $today_day_number + 1;
                $week[2] = $today_day_number + 2;
                $week[3] = $today_day_number + 3;
                $week[4] = $today_day_number + 4;
                $week[5] = $today_day_number + 5;
                $week[6] = $today_day_number + 6;
                break;
            case 1:
                $week[0] = $today_day_number - 1;
                $week[1] = $today_day_number;
                $week[2] = $today_day_number + 1;
                $week[3] = $today_day_number + 2;
                $week[4] = $today_day_number + 3;
                $week[5] = $today_day_number + 4;
                $week[6] = $today_day_number + 5;
                break;
            case 2:
                $week[0] = $today_day_number - 2;
                $week[1] = $today_day_number - 1;
                $week[2] = $today_day_number;
                $week[3] = $today_day_number + 1;
                $week[4] = $today_day_number + 2;
                $week[5] = $today_day_number + 3;
                $week[6] = $today_day_number + 4;
                break;
            case 3:
                $week[0] = $today_day_number - 3;
                $week[1] = $today_day_number - 2;
                $week[2] = $today_day_number - 1;
                $week[3] = $today_day_number;
                $week[4] = $today_day_number + 1;
                $week[5] = $today_day_number + 2;
                $week[6] = $today_day_number + 3;
                break;
            case 4:
                $week[0] = $today_day_number - 4;
                $week[1] = $today_day_number - 3;
                $week[2] = $today_day_number - 2;
                $week[3] = $today_day_number - 1;
                $week[4] = $today_day_number;
                $week[5] = $today_day_number + 1;
                $week[6] = $today_day_number + 2;
                break;
            case 5:
                $week[0] = $today_day_number - 5;
                $week[1] = $today_day_number - 4;
                $week[2] = $today_day_number - 3;
                $week[3] = $today_day_number - 2;
                $week[4] = $today_day_number - 1;
                $week[5] = $today_day_number;
                $week[6] = $today_day_number + 1;
                break;
            case 6:
                $week[0] = $today_day_number - 6;
                $week[1] = $today_day_number - 5;
                $week[2] = $today_day_number - 4;
                $week[3] = $today_day_number - 3;
                $week[4] = $today_day_number - 2;
                $week[5] = $today_day_number - 1;
                $week[6] = $today_day_number;
                break;
        }
        $data['events'] = null;
        $data['today'] = $today;
        $data['today_day_number'] = $today_day_number;
        $data['week'] = $week;
        $data['day_of_week'] = $day_of_week;

        return view('calendar.calendar', $data);
    }
}
