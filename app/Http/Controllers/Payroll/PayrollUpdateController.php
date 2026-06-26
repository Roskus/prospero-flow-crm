<?php

declare(strict_types=1);

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\MainController;
use App\Models\Payroll;
use Illuminate\Http\Request;

class PayrollUpdateController extends MainController
{
    public function update(Request $request, int $id)
    {
        $payroll = Payroll::find($id);
        $data['payroll'] = $payroll;

        return view('payroll.payroll', $data);
    }
}
