<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use Squire\Models\Country;
class CustomerController extends MainController
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $customer = new Customer();
        $data['customers'] = $customer->getAllByCompanyId(Auth::user()->company_id);
        return view('customer.index', $data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function add(Request $request)
    {
        $customer = new Customer();
        $data['customer'] = $customer;
        $data['countries'] = Country::all();
        return view('customer.customer', $data);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, int $id)
    {
        $customer = Customer::find($id);
        $data['customer'] = $customer;
        $data['countries'] = Country::all();
        return view('customer.customer', $data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save(Request $request)
    {
        if (empty($request->id)) {
            $customer = new Customer();
        } else {
            $customer = Customer::find($request->id);
        }
        $customer->company_id = Auth::user()->company_id;
        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->phone = $request->phone;
        $customer->email = $request->email;
        $customer->country_id = $request->country_id;
        $customer->save();
        return redirect('/customer');
    }
}
