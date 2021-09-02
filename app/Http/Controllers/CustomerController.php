<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;

class CustomerController extends MainController
{
    public function index(Request $request)
    {
        $customer = new Customer();
        $data['customers'] = $customer->getAll();
        return view('customer.index', $data);
    }

    public function add(Request $request)
    {
        $customer = new Customer();
        $data['customer'] = $customer;
        return view('customer.customer', $data);
    }

    public function edit(Request $request, int $id)
    {
        $customer = Customer::find($id);
        $data['customer'] = $customer;
        return view('customer.customer', $data);
    }

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
        $customer->save();
        return redirect('/customer');
    }
}
