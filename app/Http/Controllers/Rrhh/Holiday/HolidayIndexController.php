<?php

declare(strict_types=1);

namespace App\Http\Controllers\Rrhh\Holiday;

use App\Http\Controllers\MainController;
use App\Models\CompanyHoliday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HolidayIndexController extends MainController
{
    public function index(Request $request)
    {
        $year = $request->get('year', date('Y'));

        $data['holidays'] = CompanyHoliday::where('company_id', Auth::user()->company_id)
            ->whereYear('date', $year)
            ->orderBy('date')
            ->get();

        $data['year'] = $year;

        return view('rrhh.holiday.index', $data);
    }
}
