<?php

declare(strict_types=1);

namespace App\Http\Controllers\Rrhh\TimeEntry;

use App\Http\Controllers\MainController;
use App\Models\WorkHour;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimeEntryUpdateController extends MainController
{
    public function update(Request $request, int $id): RedirectResponse
    {
        $entry = WorkHour::where('user_id', Auth::user()->id)->findOrFail($id);

        $validated = $request->validate([
            'entry_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'notes' => 'nullable|string|max:500',
        ]);

        $start = Carbon::parse($validated['entry_date'].' '.$validated['start_time']);
        $end = Carbon::parse($validated['entry_date'].' '.$validated['end_time']);

        $entry->update([
            'start_time' => $start,
            'end_time' => $end,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect('/rrhh/time-entries')
            ->with(['status' => 'success', 'message' => __('Time entry updated successfully')]);
    }
}
