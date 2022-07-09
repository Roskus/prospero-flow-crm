<?php

namespace App\Http\Controllers\Lead;

use Illuminate\Http\Request;
use Squire\Models\Country;
use App\Models\Lead;

class LeadCreateController
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        $lead = new Lead();
        $data['lead'] = $lead;
        $data['countries'] = Country::orderBy('name')->get();
        return view('lead.lead', $data);
    }
}
