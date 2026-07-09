<?php

declare(strict_types=1);

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\MainController;
use App\Models\Industry;
use App\Models\Lead;
use App\Models\Source;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Squire\Models\Country;

class LeadUpdateController extends MainController
{
    /**
     * @return Application|Factory|View
     */
    public function update(Request $request, int $id)
    {
        $lead = Lead::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $industry = new Industry;
        $user = new User;
        $data['lead'] = $lead;
        $data['countries'] = Country::orderBy('name')->get();
        // Temporary fix get this from configuration
        if (Auth::user()->company_id == 3) {
            $industries = $industry->getAllByCompany((int) Auth::user()->company_id);
        } else {
            $industries = $industry->getAll();
        }
        $data['industries'] = $industries;
        $data['sellers'] = $user->getAllActiveByCompany(Auth::user()->company_id);
        $data['sources'] = Source::all();
        $data['editorType'] = 'advanced';

        return view('lead.lead', $data);
    }
}
