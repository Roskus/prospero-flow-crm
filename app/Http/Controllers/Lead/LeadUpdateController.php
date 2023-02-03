<?php

declare(strict_types=1);

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\MainController;
use App\Models\Industry;
use App\Models\Lead;
use Illuminate\Http\Request;
use Squire\Models\Country;

class LeadUpdateController extends MainController
{
    /**
     * @param  Request  $request
     * @param  int  $id
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
