<?php

declare(strict_types=1);

namespace App\Http\Controllers\Rrhh\TimeOff;

use App\Http\Controllers\MainController;
use App\Models\TimeOff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TimeOffIndexController extends MainController
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $employeeId = $request->get('user_id');

        $query = TimeOff::where('company_id', $companyId);

        if ($employeeId) {
            $query->where('user_id', $employeeId);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data['requests'] = $query->with('user')->orderBy('created_at', 'desc')->paginate(20);
        $data['company'] = Auth::user()->company;

        $userId = Auth::user()->id;
        $records = DB::table('time_off')
            ->where('user_id', $userId)
            ->whereIn('status', ['approved', 'pending'])
            ->get();

        $usedVacation = 0;
        $usedPersonal = 0;

        foreach ($records as $r) {
            if ($r->type === 'vacation') {
                $usedVacation += (float) $r->days_used;
            }
            if ($r->type === 'personal') {
                $usedPersonal += (float) $r->days_used;
            }
        }

        $annualVacation = Auth::user()->vacation_days_override ?? $data['company']->vacation_days_per_year;
        $annualPersonal = $data['company']->personal_days_per_year;

        $data['available_vacation'] = $annualVacation - $usedVacation;
        $data['annual_vacation'] = $annualVacation;
        $data['available_personal'] = $annualPersonal - $usedPersonal;
        $data['annual_personal'] = $annualPersonal;

        return view('rrhh.time_off.index', $data);
    }
}
