<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\MainController;
use App\Models\Customer;
use App\Models\Industry;
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
        $industry = new Industry();
        $data['customer'] = $customer;
        $data['countries'] = Country::orderBy('name')->get();
        $data['industries'] = $industry->getAll();

        return view('customer.customer', $data);
    }
}
