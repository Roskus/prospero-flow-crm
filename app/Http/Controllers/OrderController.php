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
}
