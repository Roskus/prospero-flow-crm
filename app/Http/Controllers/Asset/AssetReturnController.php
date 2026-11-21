<?php

declare(strict_types=1);

namespace App\Http\Controllers\Asset;

use App\Http\Controllers\MainController;
use App\Http\Requests\AssetReturnRequest;
use App\Models\Asset;
use App\Services\AssetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AssetReturnController extends MainController
{
    public function return(AssetReturnRequest $request, int $id, AssetService $assetService): RedirectResponse
    {
        $asset = Asset::query()->ofCompany((int) Auth::user()->company_id)->findOrFail($id);
        $data = $request->validated();

        $assetService->return($asset, $data['end_date'] ?? null, $data['notes'] ?? null);

        return redirect()->route('asset.show', $asset->id)
            ->with(['status' => 'success', 'message' => __('Asset returned successfully')]);
    }
}
