<?php

declare(strict_types=1);

namespace App\Http\Controllers\Rrhh\TimeEntry;

use App\Http\Controllers\MainController;
use App\Models\WorkHour;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TimeEntrySaveController extends MainController
{
    public function save(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:user,id',
            'entry_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'notes' => 'nullable|string|max:500',
        ]);

        $start = Carbon::parse($validated['entry_date'].' '.$validated['start_time']);
        $end = Carbon::parse($validated['entry_date'].' '.$validated['end_time']);

        WorkHour::create([
            'user_id' => $validated['user_id'],
            'start_time' => $start,
            'end_time' => $end,
            'type' => 'work',
            'is_manual' => true,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect('/rrhh/time-entries?user_id='.$validated['user_id'])
            ->with(['status' => 'success', 'message' => __('Time entry saved successfully')]);
    }
}
