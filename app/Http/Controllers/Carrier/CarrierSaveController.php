<?php

declare(strict_types=1);

namespace App\Http\Controllers\Carrier;

use App\Http\Controllers\MainController;
use App\Http\Requests\CarrierSaveRequest;
use App\Models\Carrier;
use App\Services\AssetService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CarrierSaveController extends MainController
{
    /**
     * Name and country are unique per company: the check is repeated under the company lock, so a
     * double submit cannot create two equal carriers; deleted carriers keep their name free.
     */
    public function save(CarrierSaveRequest $request, AssetService $assetService): RedirectResponse
    {
        $data = $request->validated();
        $companyId = (int) Auth::user()->company_id;

        DB::transaction(function () use ($data, $companyId, $assetService): void {
            $assetService->lockCompany($companyId);

            $carrier = empty($data['id'])
                ? new Carrier
                : Carrier::query()->ofCompany($companyId)->findOrFail($data['id']);

            $nameTaken = Carrier::query()->ofCompany($companyId)
                ->where('name', $data['name'])
                ->where(fn (Builder $query) => empty($data['country_id'])
                    ? $query->whereNull('country_id')
                    : $query->where('country_id', $data['country_id']))
                ->when($carrier->exists, fn (Builder $query) => $query->whereKeyNot($carrier->id))
                ->exists();

            if ($nameTaken) {
                throw ValidationException::withMessages(['name' => __('A carrier with this name already exists in this country.')]);
            }

            $carrier->company_id = $companyId;
            $carrier->fill(Arr::only($data, ['name', 'country_id', 'phone', 'website']));
            $carrier->save();
        });

        return redirect('/carrier')->with(['status' => 'success', 'message' => __('Carrier saved successfully')]);
    }
}
