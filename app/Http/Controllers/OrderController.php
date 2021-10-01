<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;

class OrderController extends MainController
{
    public function index(Request $request)
    {
        $order = new Order();
        $data['orders'] = $order->getAllActiveByCompany(Auth::user()->company_id);
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
        $company_id = Auth::user()->company_id;
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
        $order->setCompanyId((int) Auth::user()->company_id);
        $order->setCustomerId((int) $request->customer_id);
        $order->setAmount((float) $request->total);
        try {
            $status = $order->save();
        } catch (\Throwable $t) {
            Log::error($t->getMessage());
        }
        return redirect('order');
    }

}
