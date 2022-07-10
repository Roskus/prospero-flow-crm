<?php
namespace App\Http\Controllers\Lead;

use Illuminate\Http\Request;
use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Auth;
use App\Models\Lead;
use Squire\Models\Country;

class LeadIndexController extends MainController
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $lead = new Lead();
        $data['leads'] = $lead->getAllByCompanyId(Auth::user()->company_id, $search);
        $data['search'] = $search;
        return view('lead.index', $data);
    }
}
