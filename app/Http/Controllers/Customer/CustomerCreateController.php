<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\MainController;
use App\Models\Customer;
use Illuminate\Http\Request;
use Squire\Models\Country;

class CustomerCreateController extends MainController
{
    /**
     * @param  Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        $customer = new Customer();
        $data['customer'] = $customer;
        $data['countries'] = Country::all();

        return view('customer.customer', $data);
    }
}
