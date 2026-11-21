<?php

declare(strict_types=1);

namespace App\Http\Controllers\Carrier;

use App\Http\Controllers\MainController;
use App\Models\Carrier;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Squire\Models\Country;

class CarrierUpdateController extends MainController
{
    public function update(int $id): View
    {
        $carrier = Carrier::ofCompany((int) Auth::user()->company_id)->findOrFail($id);

        return view('carrier.carrier', [
            'carrier' => $carrier,
            'countries' => Country::orderBy('name')->get(),
        ]);
    }
}
