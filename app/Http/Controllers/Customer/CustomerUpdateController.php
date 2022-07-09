<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\MainController;
use App\Models\Customer;
use Illuminate\Http\Request;
use Squire\Models\Country;

class CustomerUpdateController extends MainController
{
    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function update(Request $request, int $id)
    {
        $customer = Customer::find($id);
        $data['customer'] = $customer;
        $data['countries'] = Country::all();
        return view('customer.customer', $data);
    }
}
