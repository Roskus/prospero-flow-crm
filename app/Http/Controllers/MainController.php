<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\Lead;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

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
        $data['leads'] = $lead->getLatestByCompany(Auth::user()->company_id, 4);

        $date = Carbon::now();
        $startOfCalendar = $date->copy()->firstOfMonth()->startOfWeek(Carbon::MONDAY);
        $endOfCalendar = $date->copy()->lastOfMonth()->endOfWeek(Carbon::SUNDAY);

        $data['events'] = Calendar::whereUserId(auth()->id())
            ->whereBetween('start_date', [$startOfCalendar, $endOfCalendar])
            ->get();
        $data['startOfCalendar'] = $startOfCalendar;
        $data['endOfCalendar'] = $endOfCalendar;

        return view('dashboard', $data);
    }
}
