<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Lead;

class MainController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth');
        //$locale = 'es';//App::getLocale();
        //App::setLocale($locale);
    }

    public function index(Request $request)
    {
        $order = new Order();
        $lead = new Lead();
        $data['order_count'] = $order->getPendingCount(Auth::user()->company_id);
        $data['lead_count'] = $lead->getCountByCompany(Auth::user()->company_id);
        return view('dashboard', $data);
    }
}
