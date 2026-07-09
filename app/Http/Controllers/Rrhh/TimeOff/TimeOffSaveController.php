<?php

declare(strict_types=1);

namespace App\Http\Controllers\Rrhh\TimeOff;

use App\Http\Controllers\MainController;
use App\Models\TimeOff;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TimeOffSaveController extends MainController
{
    public function save(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:vacation,sick,personal',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
        $daysUsed = $start->diffInDays($end) + 1;

        $company = Auth::user()->company;

        if ($validated['type'] === 'vacation') {
            $annualDays = Auth::user()->vacation_days_override ?? $company->vacation_days_per_year;
            $usedDays = $this->getUsedDays(Auth::user()->id, 'vacation');
            if (($usedDays + $daysUsed) > $annualDays) {
                return redirect()->back()->withErrors(
                    __('Insufficient vacation days. You have :remaining remaining.', ['remaining' => $annualDays - $usedDays])
                )->withInput();
            }
        }

        if ($validated['type'] === 'personal') {
            $annualDays = $company->personal_days_per_year;
            $usedDays = $this->getUsedDays(Auth::user()->id, 'personal');
            if (($usedDays + $daysUsed) > $annualDays) {
                return redirect()->back()->withErrors(
                    __('Insufficient personal days. You have :remaining remaining.', ['remaining' => $annualDays - $usedDays])
                )->withInput();
            }
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('timeoff', 'public');
        }

        TimeOff::create([
            'company_id' => $company->id,
            'user_id' => Auth::user()->id,
            'type' => $validated['type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'days_used' => $daysUsed,
            'reason' => $validated['reason'],
            'attachment' => $attachmentPath,
            'status' => 'pending',
        ]);

        return redirect('/rrhh/time-off')->with(['status' => 'success', 'message' => __('Time off request submitted successfully')]);
    }

    private function getUsedDays(int $userId, string $type): float
    {
        $records = DB::table('time_off')
            ->where('user_id', $userId)
            ->where('type', $type)
            ->whereIn('status', ['approved', 'pending'])
            ->get();

        $total = 0;
        foreach ($records as $r) {
            $total += (float) $r->days_used;
        }

        return $total;
    }
}
