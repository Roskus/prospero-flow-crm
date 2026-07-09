<?php

declare(strict_types=1);

namespace App\Http\Controllers\Rrhh\Schedule;

use App\Http\Controllers\MainController;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;

class ScheduleSaveController extends MainController
{
    public function save(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:user,id',
            'day_of_week' => 'required|integer|between:1,7',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'type' => 'required|in:work,break',
        ]);

        WorkSchedule::create($validated);

        return redirect('/rrhh/schedule?user_id='.$validated['user_id'])
            ->with(['status' => 'success', 'message' => __('Schedule saved successfully')]);
    }
}
