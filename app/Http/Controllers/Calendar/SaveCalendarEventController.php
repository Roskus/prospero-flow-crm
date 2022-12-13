<?php

namespace App\Http\Controllers\Calendar;

use App\Http\Controllers\MainController;
use App\Models\Calendar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaveCalendarEventController extends MainController
{
    /**
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Request $request)
    {
        //TODO validate

        if (empty($request->id)) {
            $calendar = new Calendar();
        } else {
            $calendar = Calendar::find($request->id);
        }

        $calendar->company_id = Auth::user()->company_id;
        $calendar->user_id = Auth::user()->id;
        $calendar->start_date = Carbon::create($request->date.' '.$request->start_time, Auth::user()->timezone)->setTimezone('UTC');
        $calendar->end_date = Carbon::create($request->date.' '.$request->end_time, Auth::user()->timezone)->setTimezone('UTC');
        $calendar->is_all_day = $request->is_all_day;
        $calendar->title = $request->title;
        $calendar->description = $request->description;
        $calendar->guests = $request->guest_list;
        $calendar->meeting = $request->meeting;
        $calendar->address = $request->address;
        $calendar->latitude = $request->latitude;
        $calendar->longitude = $request->longitude;
        $calendar->created_at = now();
        $calendar->save();

        return back();
    }
}
