<?php

declare(strict_types=1);

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\MainController;
use App\Models\Payroll;
use Illuminate\Http\Request;

class PayrollIndexController extends MainController
{
    public function index(Request $request)
    {
        $currentYear = (int) date('Y');
        $year = request('year', $currentYear);
        $payroll = new Payroll;
        $data['years'] = range($currentYear, $currentYear - 5);
        $data['payrolls'] = $payroll->getAllByYear((int) $year);

        return view('payroll.index', $data);
    }
}
