<?php

declare(strict_types=1);

namespace App\Http\Controllers\Asset\Maintenance;

use App\Http\Controllers\MainController;
use App\Models\AssetMaintenance;
use App\Models\Supplier;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AssetMaintenanceUpdateController extends MainController
{
    public function update(int $id): View
    {
        $company = Auth::user()->company;
        $maintenance = AssetMaintenance::whereHas('asset', fn (Builder $query) => $query->where('company_id', $company->id))
            ->with('asset.vehicle')
            ->findOrFail($id);

        return view('asset.maintenance', [
            'asset' => $maintenance->asset,
            'maintenance' => $maintenance,
            'suppliers' => Supplier::where('company_id', $company->id)->orderBy('name')->get(['id', 'name']),
            'currencies' => $company->usableCurrencies(array_filter([$maintenance->currency])),
        ]);
    }
}
