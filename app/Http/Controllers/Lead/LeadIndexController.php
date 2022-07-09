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
        $lead = new Lead();
        $data['leads'] = $lead->getAllByCompanyId(Auth::user()->company_id);
        return view('lead.index', $data);
    }
}
