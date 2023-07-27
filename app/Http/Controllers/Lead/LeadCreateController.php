<?php

declare(strict_types=1);

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\MainController;
use App\Models\Industry;
use App\Models\Lead;
use App\Models\Source;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Squire\Models\Country;

class LeadCreateController extends MainController
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        $lead = new Lead();
        $industry = new Industry();
        $user = new User();
        $data['lead'] = $lead;
        $data['countries'] = Country::orderBy('name')->get();
        // Temporary fix get this from configuration
        $data['industries'] = (Auth::user()->company_id == 3) ? $industry->getAllByCompany(Auth::user()->company_id) : $industry->getAll();
        $data['sellers'] = $user->getAllActiveByCompany(Auth::user()->company_id);
        $data['sources'] = Source::all();
        $data['editorType'] = 'advanced';

        return view('lead.lead', $data);
    }
}
