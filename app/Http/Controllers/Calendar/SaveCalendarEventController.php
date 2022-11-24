<?php
namespace App\Http\Controllers\Calendar;

use \App\Http\Controllers\MainController;
use Illuminate\Http\Request;
use App\Models\Calendar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SaveCalendarEventController extends MainController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $status = 400;
        $calendar = new Calendar();
        $calendar->company_id = Auth::user()->company_id;
        $calendar->user_id = Auth::user()->id;
        $calendar->start_date = $request->start_date;
        $calendar->end_date = $request->end_date;
        $calendar->is_all_day = $request->is_all_day;
        $calendar->title = $request->title;
        $calendar->description = $request->description;
        $calendar->guests = json_encode($request->guests);
        $calendar->meeting = $request->meeting;
        $calendar->latitude = $request->latitude;
        $calendar->longitude = $request->longitude;
        $calendar->created_at = now();
        try {
            $calendar->save();
            $status = 201;
        } catch (\Throwable $t) {
            Log::error($t->getMessage());
        }

        return response()->json([], $status);
    }
}
