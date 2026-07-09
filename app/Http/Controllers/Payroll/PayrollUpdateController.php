<?php

declare(strict_types=1);

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\MainController;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayrollUpdateController extends MainController
{
    public function update(Request $request, int $id)
    {
        $payroll = Payroll::findOrFail($id);
        $data['payroll'] = $payroll;
        $data['employees'] = User::where('company_id', Auth::user()->company_id)
            ->where('is_employee', true)
            ->orderBy('first_name')
            ->get();
        $data['current_year'] = date('Y');

        return view('payroll.payroll', $data);
    }
}
