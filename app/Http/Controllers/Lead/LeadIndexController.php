<?php

declare(strict_types=1);

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\MainController;
use App\Models\Industry;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Squire\Models\Country;

class LeadIndexController extends MainController
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $filters = [];
        $search = $request->search;
        $user = new User();

        if ($request->country_id) {
            $filters['country_id'] = $request->country_id;
            $data['country_id'] = $request->country_id;
        }

        if ($request->seller_id) {
            $filters['seller_id'] = $request->seller_id;
            $data['seller_id'] = $request->seller_id;
        }

        if ($request->status) {
            $filters['status'] = $request->status;
        }

        if ($request->industry_id) {
            $filters['industry_id'] = $request->industry_id;
        }

        if (Auth::user()->hasRole('Seller')) {
            $filters['seller_id'] = Auth::user()->id;
        }

        $customer = new Customer();
        $data['colors'] = config('color');
        $data['bootstrap_colors'] = ['text-bg-primary', 'text-bg-secondary', 'text-bg-success', 'text-bg-danger', 'text-bg-warning', 'text-bg-info',];
        $data['countries'] = Country::orderBy('name')->get();
        $data['lead_count'] = Lead::where('company_id', Auth::user()->company_id)->count();
        $data['leads'] = $lead->getAllByCompanyId(Auth::user()->company_id, $search, $filters);
        $data['search'] = $search;
        $data['sellers'] = $user->getAllActiveByCompany(Auth::user()->company_id);
        $data['statuses'] = $lead->getStatus();
        $data['industries'] = Industry::all();

        return view('lead.index', $data);
    }
}
