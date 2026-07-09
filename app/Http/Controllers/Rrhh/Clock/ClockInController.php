<?php

declare(strict_types=1);

namespace App\Http\Controllers\Rrhh\Clock;

use App\Http\Controllers\MainController;
use App\Models\WorkHour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClockInController extends MainController
{
    public function in(Request $request): RedirectResponse
    {
        $open = WorkHour::where('user_id', Auth::user()->id)
            ->whereNull('end_time')
            ->first();

        if ($open) {
            return redirect()->back()->withErrors(__('You already have an open clock entry. Clock out first.'));
        }

        WorkHour::create([
            'user_id' => Auth::user()->id,
            'start_time' => now(),
            'type' => 'work',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->back()->with(['status' => 'success', 'message' => __('Clocked in successfully')]);
    }
}
