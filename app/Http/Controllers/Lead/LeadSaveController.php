<?php

declare(strict_types=1);

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\MainController;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadSaveController extends MainController
{
    public function save(Request $request)
    {
        $status = 'error';
        if (empty($request->id)) {
            $lead = new Lead();
            $lead->created_at = now();
            $lead->status = Lead::OPEN;
        } else {
            $lead = Lead::find($request->id);
        }
        $lead->seller_id = ($request->seller_id) ? $request->seller_id : Auth::user()->id;
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
        $lead->website = ($request->website) ? rtrim($request->website, '/') : null;
        $lead->notes = $request->notes;

        $lead->linkedin = ($request->linkedin) ? rtrim($request->linkedin, '/') : null;
        $lead->facebook = ($request->facebook) ? rtrim($request->facebook, '/') : null;
        $lead->instagram = ($request->instagram) ? rtrim($request->instagram, '/') : null;
        $lead->twitter = ($request->twitter) ? rtrim($request->twitter, '/') : null;
        $lead->youtube = ($request->youtube) ? rtrim($request->youtube, '/') : null;
        $lead->tiktok = ($request->tiktok) ? rtrim($request->tiktok, '/') : null;

        $lead->country_id = $request->country_id;
        $lead->province = $request->province;
        $lead->city = $request->city;
        $lead->locality = $request->locality;
        $lead->street = $request->street;
        $lead->zipcode = $request->zipcode;

        $lead->industry_id = $request->industry_id;
        $lead->schedule_contact = $request->schedule_contact;

        $lead->tags = ($request->tags) ? explode(',', $request->tags) : null;

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
