<?php

declare(strict_types=1);

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\MainController;
use App\Models\Lead;
use Illuminate\Support\Facades\Auth;

class LeadShowController extends MainController
{
    public function show(int $id)
    {
        $lead = Lead::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->firstOrFail();

        return view('lead_customer.show', compact('lead'));
    }
}
