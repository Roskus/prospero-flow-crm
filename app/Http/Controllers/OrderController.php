<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;

class OrderController extends MainController
{
    public function index(Request $request)
    {
        $order = new Order();
        $data['orders'] = $order->getAll();
        return view('order/index',$data);
    }

    public function add(Request $request)
    {
        $order = new Order();
        $customer = new Customer();
        $product = new Product();
        $company_id = 1; //TODO get current Auth::user()->current_company_id
        $data['order'] = $order;
        $data['customers'] = $customer->getAllByCompanyId($company_id);
        $data['products'] = $product->getAllByCompanyId($company_id);
        return view('order/order',$data);
    }

    public function edit(Request $request,int $id)
    {
        $order = Order::find($id);
        $data['order'] = $order;
        return view('order/index',$data);
    }

    public function save(Request $request)
    {


    }

}
