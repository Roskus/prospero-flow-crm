<?php

declare(strict_types=1);

namespace App\Http\Controllers\Carrier;

use App\Http\Controllers\MainController;
use App\Models\Carrier;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Squire\Models\Country;

class CarrierCreateController extends MainController
{
    public function create(): View
    {
        return view('carrier.carrier', [
            'carrier' => new Carrier(['country_id' => Auth::user()->company->country_id]),
            'countries' => Country::orderBy('name')->get(),
        ]);
    }
}
