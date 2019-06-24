<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends MainController
{
    public function index()
    {
        $order = new Order();
        $data['orders'] = $order->getAll();
        return view('order/index',$data);
    }

    public function add()
    {
        $order = new Order();
        $data['order'] = $order;
        return view('order/order',$data);
    }

    public function edit(Request $request,int $id)
    {
        $order = Order::find($id);
        $data['order'] = $order;
        return view('order/index',$data);
    }

    public function save()
    {


    }

}
