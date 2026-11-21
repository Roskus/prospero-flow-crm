<?php

declare(strict_types=1);

namespace App\Http\Controllers\Asset;

use App\Enums\AssetCategory;
use App\Http\Controllers\MainController;
use App\Models\Asset;
use App\Services\AssetService;
use Illuminate\Contracts\View\View;

class AssetCreateController extends MainController
{
    public function create(string $category, AssetService $assetService): View
    {
        $asset = new Asset(['category' => AssetCategory::from($category)]);

        return view('asset.asset', $assetService->formData($asset));
    }
}
