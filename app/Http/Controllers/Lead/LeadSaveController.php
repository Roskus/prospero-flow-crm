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
        $status  = 'error';
        if (empty($request->id)) {
            $lead = new Lead();
            $lead->seller_id = Auth::user()->id;
            $lead->created_at = now();
        } else {
            $lead = Lead::find($request->id);
        }
        $lead->company_id = Auth::user()->company_id;
        $lead->name = $request->name;
        $lead->business_name = $request->business_name;
        $lead->phone = $request->phone;
        $lead->mobile = $request->mobile;
        $lead->email = $request->email;
        $lead->website = rtrim($request->website,'/');
        $lead->notes = $request->notes;

        $lead->linkedin = $request->linkedin;
        $lead->facebook = $request->facebook;
        $lead->instagram = $request->instagram;
        $lead->twitter = $request->twitter;
        $lead->youtube = $request->youtube;
        $lead->tiktok = $request->tiktok;

        $lead->country_id = $request->country_id;
        $lead->city = $request->city;
        $lead->street = $request->street;
        $lead->zipcode = $request->zipcode;

        $lead->industry_id = $request->industry_id;
        $lead->schedule_contact = $request->schedule_contact;

        $lead->updated_at = now();

        if($lead->save())
            $status = 'success';

        $response = [
            'status' => $status,
            'message' => 'Lead <a href="/lead/update/:id">:name</a> saved successfully',
            'name' => $lead->name,
            'id' => $lead->id
        ];
        return redirect('/lead')->with($response);
    }
}
