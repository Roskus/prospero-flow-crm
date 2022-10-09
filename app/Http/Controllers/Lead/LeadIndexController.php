<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\MainController;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Squire\Models\Country;

class LeadIndexController extends MainController
{
    /**
     * @param  Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $filters = [];
        $user = new User();
        $search = $request->search;

        if ($request->country_id) {
            $filters['country_id'] = $request->country_id;
            $data['country_id'] = $request->country_id;
        }

        if ($request->seller_id) {
            $filters['seller_id'] = $request->seller_id;
        }

        if ($request->status) {
            $filters['status'] = $request->status;
        }

        $lead = new Lead();
        $data['countries'] = Country::orderBy('name')->get();
        $data['leads'] = $lead->getAllByCompanyId(Auth::user()->company_id, $search, $filters);
        $data['search'] = $search;
        $data['sellers'] = $user->getAllActiveByCompany(Auth::user()->company_id);
        $data['statuses'] = $lead->getStatus();

        return view('lead.index', $data);
    }
}
