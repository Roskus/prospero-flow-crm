<?php

declare(strict_types=1);

namespace App\Http\Controllers\Rrhh\TimeEntry;

use App\Http\Controllers\MainController;
use App\Models\User;
use App\Models\WorkHour;
use App\Services\WorkHourService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimeEntryIndexController extends MainController
{
    public function __construct(private WorkHourService $workHourService) {}

    /**
     * Week of time entries of an employee of the active company; without `read rrhh` only the user's own.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $employee = $user->can('read rrhh') && $request->filled('user_id')
            ? User::where('company_id', $user->company_id)->findOrFail($request->integer('user_id'))
            : $user;

        $weekOffset = (int) $request->get('week', 0);
        $startOfWeek = Carbon::now()->startOfWeek()->addWeeks($weekOffset);
        $endOfWeek = Carbon::now()->endOfWeek()->addWeeks($weekOffset);

        $days = $this->workHourService->dailyHours($employee, $startOfWeek, $endOfWeek);

        $data['days'] = $days;
        $data['weekly_total'] = array_sum(array_column($days, 'total'));
        $data['weekly_estimated'] = array_sum(array_column($days, 'estimated'));
        $data['start_of_week'] = $startOfWeek;
        $data['end_of_week'] = $endOfWeek;
        $data['week_offset'] = $weekOffset;
        $data['open_entry'] = WorkHour::where('user_id', $employee->id)->whereNull('end_time')->first();
        $data['employee_id'] = $employee->id;

        return view('rrhh.time_entry.index', $data);
    }
}
