<?php

namespace App\Http\Controllers\Lead;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\MainController;
use App\Models\Lead;

class LeadSaveController extends MainController
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save(Request $request)
    {
        if (empty($request->id)) {
            $lead = new Lead();
            $lead->seller_id = Auth::user()->id;
            $lead->created_at = now();
        } else {
            $lead = Lead::find($request->id);
        }
        $lead->company_id = Auth::user()->company_id;
        $lead->first_name = $request->first_name;
        $lead->last_name = $request->last_name;
        $lead->phone = $request->phone;
        $lead->email = $request->email;
        $lead->country_id = $request->country_id;
        $lead->website = $request->website;
        $lead->notes = $request->notes;
        $lead->updated_at = now();
        $lead->save();
        return redirect('/lead');
    }
}
