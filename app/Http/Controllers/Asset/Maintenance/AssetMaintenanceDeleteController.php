<?php

declare(strict_types=1);

namespace App\Http\Controllers\Asset\Maintenance;

use App\Http\Controllers\MainController;
use App\Models\AssetMaintenance;
use App\Services\AssetService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AssetMaintenanceDeleteController extends MainController
{
    public function delete(int $id, AssetService $assetService): RedirectResponse
    {
        $maintenance = AssetMaintenance::whereHas('asset', fn (Builder $query) => $query->where('company_id', Auth::user()->company_id))
            ->findOrFail($id);
        $assetId = $maintenance->asset_id;

        $assetService->deleteMaintenance($maintenance);

        return redirect()->route('asset.show', $assetId)
            ->with(['status' => 'success', 'message' => __('Maintenance deleted successfully')]);
    }
}
