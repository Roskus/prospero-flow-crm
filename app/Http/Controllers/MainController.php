<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
//use App\Models\Season;
use App\Models\Order;

class MainController extends Controller
{
    public function index()
    {
        $order = new Order();
        $data['order_count'] = $order->getPendingCount();
        return view('dashboard');
    }
}
