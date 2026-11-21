<?php

declare(strict_types=1);

namespace App\Http\Controllers\Carrier;

use App\Http\Controllers\MainController;
use App\Http\Requests\CarrierSaveRequest;
use App\Models\Carrier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class CarrierSaveController extends MainController
{
    public function save(CarrierSaveRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $companyId = (int) Auth::user()->company_id;

        $carrier = empty($data['id'])
            ? new Carrier
            : Carrier::ofCompany($companyId)->findOrFail($data['id']);

        $carrier->company_id = $companyId;
        $carrier->fill(Arr::only($data, ['name', 'country_id', 'phone', 'website']));
        $carrier->save();

        return redirect('/carrier')->with(['status' => 'success', 'message' => __('Carrier saved successfully')]);
    }
}
