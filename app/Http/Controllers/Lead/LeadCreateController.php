<?php

namespace App\Http\Controllers\Lead;

use App\Models\Industry;
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
        $industry = new Industry();
        $data['lead'] = $lead;
        $data['countries'] = Country::orderBy('name')->get();
        $data['industries'] = $industry->getAll();
        return view('lead.lead', $data);
    }
}
