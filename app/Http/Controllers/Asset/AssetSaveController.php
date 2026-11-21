<?php

declare(strict_types=1);

namespace App\Http\Controllers\Asset;

use App\Http\Controllers\MainController;
use App\Http\Requests\AssetSaveRequest;
use App\Models\Asset;
use App\Services\AssetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AssetSaveController extends MainController
{
    public function save(AssetSaveRequest $request, AssetService $assetService): RedirectResponse
    {
        $asset = $assetService->save(
            $request->editedAsset() ?? new Asset,
            $request->validated(),
            (int) Auth::user()->company_id,
            $request->file('photo'),
        );

        return redirect()->route('asset.show', $asset->id)
            ->with(['status' => 'success', 'message' => __('Asset saved successfully')]);
    }
}
