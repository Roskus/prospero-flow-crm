<?php

declare(strict_types=1);

namespace App\Http\Controllers\Report;

use App\Http\Controllers\MainController;
use Illuminate\Http\Request;

class ReportIndexController extends MainController
{
    public function index(Request $request)
    {
        return view('report.index');
    }
}
