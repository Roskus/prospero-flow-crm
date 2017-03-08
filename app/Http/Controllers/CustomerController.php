<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends MainController
{
    public function index()
    {
        $customer = new Customer();
        $data['customers'] = $customer->getAll();
        return view('customer/index',$data); 
    }
}