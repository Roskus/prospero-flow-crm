<?php

declare(strict_types=1);

namespace App\Http\Controllers\Rrhh\Schedule;

use App\Http\Controllers\MainController;
use App\Models\WorkSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ScheduleUpdateController extends MainController
{
    public function update(Request $request, int $id): RedirectResponse
    {
        $schedule = WorkSchedule::findOrFail($id);

        $validated = $request->validate([
            'day_of_week' => 'required|integer|between:1,7',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'type' => 'required|in:work,break',
        ]);

        $schedule->update($validated);

        return redirect('/rrhh/schedule?user_id='.$schedule->user_id)
            ->with(['status' => 'success', 'message' => __('Schedule updated successfully')]);
    }
}
