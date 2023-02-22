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

class LeadUpdateController extends MainController
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function update(Request $request, int $id)
    {
        $lead = Lead::find($id);
        $industry = new Industry();
        $user = new User();
        $data['lead'] = $lead;
        $data['countries'] = Country::orderBy('name')->get();
        $data['industries'] = $industry->getAll();
        $data['sellers'] = $user->getAllActiveByCompany(Auth::user()->company_id);
        $data['editorType'] = 'advanced';

        return view('lead.lead', $data);
    }
}
