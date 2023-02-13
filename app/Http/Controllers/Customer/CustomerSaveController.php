<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\MainController;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerSaveController extends MainController
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save(Request $request)
    {
        $status = 'error';
        if (empty($request->id)) {
            $customer = new Customer();
            $customer->created_at = now();
        } else {
            $customer = Customer::find($request->id);
        }
        $customer->seller_id = ($request->seller_id) ? $request->seller_id : Auth::user()->id;
        $customer->company_id = Auth::user()->company_id;
        $customer->name = $request->name;
        $customer->business_name = $request->business_name;
        $customer->dob = $request->dob;
        $customer->vat = $request->vat;
        $customer->phone = $request->phone;
        $customer->phone2 = $request->phone2;
        $customer->mobile = $request->mobile;
        $customer->email = $request->email;
        $customer->email2 = $request->email2;
        $customer->website = ($request->website) ? rtrim($request->website, '/') : null;
        $customer->notes = $request->notes;

        $customer->linkedin = ($request->linkedin) ? rtrim($request->linkedin, '/') : null;
        $customer->facebook = ($request->facebook) ? rtrim($request->facebook, '/') : null;
        $customer->instagram = ($request->instagram) ? rtrim($request->instagram, '/') : null;
        $customer->twitter = ($request->twitter) ? rtrim($request->twitter, '/') : null;
        $customer->youtube = ($request->youtube) ? rtrim($request->youtube, '/') : null;
        $customer->tiktok = ($request->tiktok) ? rtrim($request->tiktok, '/') : null;

        $customer->country_id = ($request->country_id) ? $request->country_id : Auth::user()->company->country_id;
        $customer->province = $request->province;
        $customer->city = $request->city;
        $customer->locality = $request->locality;
        $customer->street = $request->street;
        $customer->zipcode = $request->zipcode;

        $customer->industry_id = $request->industry_id;
        $customer->schedule_contact = $request->schedule_contact;

        $customer->tags = ($request->tags) ? explode(',', $request->tags) : null;

        if ($request->status) {
            $customer->status = $request->status;
        }
        $customer->updated_at = now();

        if ($customer->save()) {
            $status = 'success';
        }

        $response = [
            'status' => $status,
            'message' => 'Customer :name successfully saved',
            'name' => $customer->name,
            'id' => $customer->id,
        ];

        return redirect('/customer')->with($response);
    }
}
