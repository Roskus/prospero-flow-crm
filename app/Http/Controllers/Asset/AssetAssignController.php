<?php

declare(strict_types=1);

namespace App\Http\Controllers\Asset;

use App\Http\Controllers\MainController;
use App\Http\Requests\AssetAssignRequest;
use App\Models\Asset;
use App\Services\AssetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AssetAssignController extends MainController
{
    public function assign(AssetAssignRequest $request, int $id, AssetService $assetService): RedirectResponse
    {
        $asset = Asset::query()->ofCompany((int) Auth::user()->company_id)->findOrFail($id);
        $data = $request->validated();

        $assetService->assign($asset, (int) $data['user_id'], $data['start_date'] ?? null, $data['notes'] ?? null, (int) Auth::id());

        return redirect()->route('asset.show', $asset->id)
            ->with(['status' => 'success', 'message' => __('Asset assigned successfully')]);
    }
}
