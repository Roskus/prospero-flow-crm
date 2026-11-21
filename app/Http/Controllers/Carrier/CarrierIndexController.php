<?php

declare(strict_types=1);

namespace App\Http\Controllers\Carrier;

use App\Http\Controllers\MainController;
use App\Models\Carrier;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class CarrierIndexController extends MainController
{
    public function index(): View
    {
        $carriers = Carrier::query()->ofCompany((int) Auth::user()->company_id)
            ->with('country')
            ->withCount('devices')
            ->orderBy('country_id')
            ->orderBy('name')
            ->get();

        return view('carrier.index', ['carriers' => $carriers]);
    }
}
