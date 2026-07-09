<?php

declare(strict_types=1);

namespace App\Http\Controllers\Rrhh\Schedule;

use App\Http\Controllers\MainController;
use App\Models\User;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleIndexController extends MainController
{
    public function index(Request $request)
    {
        $employeeId = $request->get('user_id', Auth::user()->id);

        $data['schedule'] = WorkSchedule::where('user_id', $employeeId)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        $data['employee_id'] = $employeeId;
        $data['employees'] = User::where('company_id', Auth::user()->company_id)
            ->where('is_employee', true)
            ->get();

        return view('rrhh.schedule.index', $data);
    }
}
