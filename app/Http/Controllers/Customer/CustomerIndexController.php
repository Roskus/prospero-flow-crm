<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\MainController;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Squire\Models\Country;

class CustomerIndexController extends MainController
{
    /**
     * @param  Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $customer = new Customer();
        $user = new User();
        $data['sellers'] = $user->getAllActiveByCompany(Auth::user()->company_id);
        $data['customers'] = $customer->getAllByCompanyId(Auth::user()->company_id);
        $data['countries'] = Country::orderBy('name')->get();
        $data['statuses'] = [];
        return view('customer.index', $data);
    }
}
