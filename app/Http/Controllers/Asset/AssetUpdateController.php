<?php

declare(strict_types=1);

namespace App\Http\Controllers\Asset;

use App\Http\Controllers\MainController;
use App\Models\Asset;
use App\Services\AssetService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class AssetUpdateController extends MainController
{
    public function update(int $id, AssetService $assetService): View
    {
        $asset = Asset::query()->ofCompany((int) Auth::user()->company_id)->with('vehicle', 'device')->findOrFail($id);

        return view('asset.asset', $assetService->formData($asset));
    }
}
