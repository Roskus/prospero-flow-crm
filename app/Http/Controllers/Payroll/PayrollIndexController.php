<?php

declare(strict_types=1);

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\MainController;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayrollIndexController extends MainController
{
    public function index(Request $request)
    {
        $currentYear = (int) date('Y');
        $year = (int) ($request->input('year', $currentYear));

        $query = Payroll::where('period_year', $year)
            ->whereHas('user', fn ($q) => $q->where('company_id', Auth::user()->company_id));

        if (Auth::user()->can('read payroll')) {
            $employeeId = $request->input('user_id');

            if ($employeeId) {
                $query->where('user_id', $employeeId);
            }
        } else {
            $query->where('user_id', Auth::user()->id);
        }

        $data['payrolls'] = $query->with('user')->orderBy('payment_date', 'desc')->get();
        $data['years'] = range($currentYear, $currentYear - 5);
        $data['employees'] = User::where('company_id', Auth::user()->company_id)
            ->where('is_employee', true)
            ->orderBy('first_name')
            ->get();
        $data['year'] = $year;

        return view('payroll.index', $data);
    }
}
