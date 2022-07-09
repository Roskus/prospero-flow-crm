<?php

namespace App\Http\Controllers\Lead;

use Illuminate\Http\Request;
use App\Http\Controllers\MainController;
use Squire\Models\Country;
use App\Models\Lead;

class LeadUpdateController extends MainController
{
    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function update(Request $request, int $id)
    {
        $lead = Lead::find($id);
        $data['lead'] = $lead;
        $data['countries'] = Country::all();
        return view('lead.lead', $data);
    }
}
