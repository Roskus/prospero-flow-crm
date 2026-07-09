<?php

declare(strict_types=1);

namespace App\Http\Controllers\Rrhh\Clock;

use App\Http\Controllers\MainController;
use App\Models\WorkHour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClockOutController extends MainController
{
    public function out(Request $request): RedirectResponse
    {
        $open = WorkHour::where('user_id', Auth::user()->id)
            ->whereNull('end_time')
            ->first();

        if (! $open) {
            return redirect()->back()->withErrors(__('No open clock entry found.'));
        }

        $open->update(['end_time' => now()]);

        return redirect()->back()->with(['status' => 'success', 'message' => __('Clocked out successfully')]);
    }
}
