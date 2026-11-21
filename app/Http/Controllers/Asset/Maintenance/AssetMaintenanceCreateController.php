<?php

declare(strict_types=1);

namespace App\Http\Controllers\Asset\Maintenance;

use App\Http\Controllers\MainController;
use App\Models\Asset;
use App\Models\AssetMaintenance;
use App\Models\Supplier;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class AssetMaintenanceCreateController extends MainController
{
    public function create(int $assetId): View
    {
        $company = Auth::user()->company;
        $asset = Asset::ofCompany($company->id)->with('vehicle')->findOrFail($assetId);

        return view('asset.maintenance', [
            'asset' => $asset,
            'maintenance' => new AssetMaintenance(['date' => today(), 'currency' => $company->currency]),
            'suppliers' => Supplier::where('company_id', $company->id)->orderBy('name')->get(['id', 'name']),
            'currencies' => $company->usableCurrencies(),
        ]);
    }
}
