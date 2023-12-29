<?php

declare(strict_types=1);

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\Controller;
use App\Models\Lead;

class LeadShowController extends Controller
{
    public function show(Lead $lead)
    {
        return view('lead_customer.show', compact('lead'));
    }
}
