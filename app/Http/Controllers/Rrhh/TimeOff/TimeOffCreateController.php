<?php

declare(strict_types=1);

namespace App\Http\Controllers\Rrhh\TimeOff;

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TimeOffCreateController extends MainController
{
    public function create()
    {
        $company = Auth::user()->company;
        $userId = Auth::user()->id;

        $records = DB::table('time_off')
            ->where('user_id', $userId)
            ->whereIn('status', ['approved', 'pending'])
            ->select('id', 'days_used', 'type', 'status')
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

        $annualVacation = Auth::user()->vacation_days_override ?? $company->vacation_days_per_year;
        $annualPersonal = $company->personal_days_per_year;

        $data['available_vacation'] = $annualVacation - $usedVacation;
        $data['annual_vacation'] = $annualVacation;
        $data['available_personal'] = $annualPersonal - $usedPersonal;
        $data['annual_personal'] = $annualPersonal;

        return view('rrhh.time_off.create', $data);
    }
}
