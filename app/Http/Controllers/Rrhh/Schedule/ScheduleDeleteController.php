<?php

declare(strict_types=1);

namespace App\Http\Controllers\Rrhh\Schedule;

use App\Http\Controllers\MainController;
use App\Models\WorkSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleDeleteController extends MainController
{
    public function delete(Request $request, int $id): RedirectResponse
    {
        $schedule = WorkSchedule::whereHas('user', function ($query) {
            $query->where('company_id', Auth::user()->company_id);
        })->findOrFail($id);
        $schedule->delete();

        return redirect('/rrhh/schedule?user_id='.$request->get('user_id'))
            ->with(['status' => 'success', 'message' => __('Schedule entry deleted successfully')]);
    }
}
