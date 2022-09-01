<?php

namespace App\Http\Controllers\Lead;

use App\Models\Industry;
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
        $industry = new Industry();
        $data['lead'] = $lead;
        $data['countries'] = Country::orderBy('name')->get();
        $data['industries'] = $industry->getAll();
        return view('lead.lead', $data);
    }
}
