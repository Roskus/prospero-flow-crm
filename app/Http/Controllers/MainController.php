<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class MainController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            'locked',
            // new Middleware('log', only: ['index']),
            // new Middleware('subscribed', except: ['store']),
        ];
    }

    public function __construct(Request $request)
    {
        // $locale = 'es';//App::getLocale();
        // App::setLocale($locale);
    }

    public function dashboard(Request $request)
    {
        $order = new Order;
        $lead = new Lead;
        $customer = new Customer;
        $companyId = (int) Auth::user()->company_id;
        $data['leads'] = $lead->getLatestByCompany($companyId, 4);
        $data['customers'] = $customer->getRecentInteractionsByCompany($companyId, (int) Auth::id(), 4);

        if (Auth::user()->hasAnyRole(['SuperAdmin', 'CompanyAdmin'])) {
            $data['sales'] = $order->getSalesSummary($companyId);
        }

        $date = Carbon::now();
        $calendarView = Auth::user()->company?->dashboard_calendar_view ?? Company::CALENDAR_VIEW_MONTH;

        if ($calendarView === Company::CALENDAR_VIEW_WEEK) {
            $startOfCalendar = $date->copy()->startOfWeek(Carbon::MONDAY);
            $endOfCalendar = $date->copy()->endOfWeek(Carbon::SUNDAY);
        } else {
            $startOfCalendar = $date->copy()->firstOfMonth()->startOfWeek(Carbon::MONDAY);
            $endOfCalendar = $date->copy()->lastOfMonth()->endOfWeek(Carbon::SUNDAY);
        }

        $data['events'] = Calendar::whereUserId(auth()->id())
            ->whereBetween('start_date', [$startOfCalendar, $endOfCalendar])
            ->get();
        $data['calendarView'] = $calendarView;
        $data['startOfCalendar'] = $startOfCalendar;
        $data['endOfCalendar'] = $endOfCalendar;

        return view('dashboard', $data);
    }
}
