<?php

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
            $customer->seller_id = Auth::user()->id;
            $customer->created_at = now();
        } else {
            $customer = Customer::find($request->id);
        }
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
        $customer->website = rtrim($request->website, '/');
        $customer->notes = $request->notes;

        $customer->linkedin = rtrim($request->linkedin, '/');
        $customer->facebook = rtrim($request->facebook, '/');
        $customer->instagram = rtrim($request->instagram, '/');
        $customer->twitter = rtrim($request->twitter, '/');
        $customer->youtube = rtrim($request->youtube, '/');
        $customer->tiktok = rtrim($request->tiktok, '/');

        $customer->country_id = $request->country_id;
        $customer->province = $request->province;
        $customer->city = $request->city;
        $customer->locality = $request->locality;
        $customer->street = $request->street;
        $customer->zipcode = $request->zipcode;

        $customer->industry_id = $request->industry_id;
        $customer->schedule_contact = $request->schedule_contact;

        $customer->tags = explode(',', $request->tags);

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
