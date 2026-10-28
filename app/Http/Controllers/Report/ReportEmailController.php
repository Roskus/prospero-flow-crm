<?php

declare(strict_types=1);

namespace App\Http\Controllers\Report;

use App\Http\Controllers\MainController;
use App\Models\Campaign;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportEmailController extends MainController
{
    public function index(): View
    {
        return view('report.email', Campaign::getEmailReport((int) Auth::user()->company_id));
    }
}
