<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\MainController;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadSaveController extends MainController
{
    /**
     * @param  Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save(Request $request)
    {
        $status = 'error';
        if (empty($request->id)) {
            $lead = new Lead();
            $lead->seller_id = Auth::user()->id;
            $lead->created_at = now();
            $lead->status = Lead::OPEN;
        } else {
            $lead = Lead::find($request->id);
        }
        $lead->company_id = Auth::user()->company_id;
        $lead->name = $request->name;
        $lead->business_name = $request->business_name;
        $lead->dob = $request->dob;
        $lead->vat = $request->vat;
        $lead->phone = $request->phone;
        $lead->phone2 = $request->phone2;
        $lead->mobile = $request->mobile;
        $lead->email = $request->email;
        $lead->email2 = $request->email2;
        $lead->website = rtrim($request->website, '/');
        $lead->notes = $request->notes;

        $lead->linkedin = rtrim($request->linkedin, '/');
        $lead->facebook = rtrim($request->facebook, '/');
        $lead->instagram = rtrim($request->instagram, '/');
        $lead->twitter = rtrim($request->twitter, '/');
        $lead->youtube = rtrim($request->youtube, '/');
        $lead->tiktok = rtrim($request->tiktok, '/');

        $lead->country_id = $request->country_id;
        $lead->province = $request->province;
        $lead->city = $request->city;
        $lead->locality = $request->locality;
        $lead->street = $request->street;
        $lead->zipcode = $request->zipcode;

        $lead->industry_id = $request->industry_id;
        $lead->schedule_contact = $request->schedule_contact;

        $lead->tags = explode(',', $request->tags);

        if ($request->status) {
            $lead->status = $request->status;
        }
        $lead->updated_at = now();

        if ($lead->save()) {
            $status = 'success';
        }

        $response = [
            'status' => $status,
            'message' => 'Lead :name successfully saved',
            'name' => $lead->name,
            'id' => $lead->id,
        ];

        return redirect('/lead')->with($response);
    }
}
