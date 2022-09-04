<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\MainController;
use Illuminate\Http\Request;

class LeadImportIndexController extends MainController
{
    public function index(Request $request)
    {
        return view('lead.import');
    }
}
