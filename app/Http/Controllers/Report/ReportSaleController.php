<?php

declare(strict_types=1);

namespace App\Http\Controllers\Report;

use App\Http\Controllers\MainController;
use App\Http\Requests\ReportSaleRequest;
use Illuminate\Contracts\View\View;

class ReportSaleController extends MainController
{
    public function index(ReportSaleRequest $request): View
    {
        return view('report.sale', $request->reportData());
    }
}
