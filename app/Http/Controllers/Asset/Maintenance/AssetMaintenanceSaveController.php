<?php

declare(strict_types=1);

namespace App\Http\Controllers\Asset\Maintenance;

use App\Http\Controllers\MainController;
use App\Http\Requests\AssetMaintenanceSaveRequest;
use App\Models\Asset;
use App\Models\AssetMaintenance;
use App\Services\AssetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AssetMaintenanceSaveController extends MainController
{
    public function save(AssetMaintenanceSaveRequest $request, AssetService $assetService): RedirectResponse
    {
        $data = $request->validated();
        $asset = Asset::query()->ofCompany((int) Auth::user()->company_id)->with('vehicle')->findOrFail($data['asset_id']);
        $maintenance = empty($data['id'])
            ? new AssetMaintenance
            : $asset->maintenances()->findOrFail($data['id']);

        $assetService->saveMaintenance($asset, $maintenance, $data, (int) Auth::id());

        return redirect()->route('asset.show', $asset->id)
            ->with(['status' => 'success', 'message' => __('Maintenance saved successfully')]);
    }
}
