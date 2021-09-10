<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;

class OrderController extends MainController
{
    public function index(Request $request)
    {
        $order = new Order();
        $data['orders'] = $order->getAll();
        return view('order/index', $data);
    }

    /**
     * Add new order
     *
     * @param Request $request HTTP request
     */
    public function add(Request $request)
    {
        $order = new Order();
        $customer = new Customer();
        $product = new Product();
        $company_id = Auth::user()->company_i1d;
        $data['order'] = $order;
        $data['customers'] = $customer->getAllByCompanyId($company_id);
        $data['products'] = $product->getAllByCompanyId($company_id);
        return view('order/order', $data);
    }

    /**
     *
     * @param Request $request
     * @param int $id
     */
    public function edit(Request $request, int $id)
    {
        $order = Order::find($id);
        $product = new Product();
        $data['order'] = $order;
        $company_id = Auth::user()->company_id;
        $data['customers'] = $order->customer->getAllByCompanyId($company_id);
        $data['products'] = $product->getAllByCompanyId($company_id);
        return view('order/order', $data);
    }

    /**
     * @param Request $request
     */
    public function save(Request $request)
    {
        $order = new Order();
        $order->customer_id = $request->customer_id;
        $order->company_id = Auth::user()->company_id;
        $order->status = Order::PENDING;
        $order->amount = $request->total;
        $order->created_at = now();
        $order->save();
        return redirect('order');
    }

}
