<?php

declare(strict_types=1);

namespace App\Http\Controllers\Report;

use App\Http\Controllers\MainController;
use App\Http\Requests\ReportAnnualSaleRequest;
use Illuminate\Contracts\View\View;

class ReportAnnualSaleController extends MainController
{
    public function index(ReportAnnualSaleRequest $request): View
    {
        return view('report.annual_sale', $request->annualReportData());
    }
}
