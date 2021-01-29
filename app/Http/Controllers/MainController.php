<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Models\Order;

class MainController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        //$locale = 'es';//App::getLocale();
        //App::setLocale($locale);
    }

    public function index()
    {
        $order = new Order();
        $data['order_count'] = $order->getPendingCount();
        return view('dashboard');
    }
}
