<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\MainController;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerSaveController extends MainController
{
    /**
     * @param  Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save(Request $request)
    {
        if (empty($request->id)) {
            $customer = new Customer();
            $customer->created_at = now();
        } else {
            $customer = Customer::find($request->id);
            $customer->updated_at = now();
        }
        $customer->company_id = Auth::user()->company_id;
        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->phone = $request->phone;
        $customer->email = $request->email;
        $customer->country_id = $request->country_id;
        $customer->company = $request->company;
        $customer->website = $request->website;
        $customer->save();

        return redirect('/customer');
    }
}
