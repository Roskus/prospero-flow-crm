<?php

declare(strict_types=1);

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\MainController;

class PayrollCreateController extends MainController
{
    public function create()
    {
        return view('payroll.payroll');
    }
}
