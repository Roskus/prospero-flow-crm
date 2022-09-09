<?php

namespace App\Http\Controllers\Calendar;

use App\Http\Controllers\MainController;
use Illuminate\Http\Request;

class CalendarController extends MainController
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $data['events'] = null;
        return view('calendar.calendar', $data);
    }
}
