<?php

declare(strict_types=1);

namespace App\Http\Controllers\Report;

use App\Http\Controllers\MainController;

class ReportSaleController extends MainController
{
    public function index()
    {
        return view('report.sale');
    }
}
