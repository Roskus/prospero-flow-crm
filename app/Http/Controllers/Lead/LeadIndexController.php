<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\MainController;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadIndexController extends MainController
{
    /**
     * @param  Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $lead = new Lead();
        $data['leads'] = $lead->getAllByCompanyId(Auth::user()->company_id, $search);
        $data['search'] = $search;

        return view('lead.index', $data);
    }
}
