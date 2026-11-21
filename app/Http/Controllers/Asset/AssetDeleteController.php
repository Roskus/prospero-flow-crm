<?php

declare(strict_types=1);

namespace App\Http\Controllers\Asset;

use App\Http\Controllers\MainController;
use App\Models\Asset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AssetDeleteController extends MainController
{
    /**
     * Soft delete keeps the assignment and maintenance history and the photo.
     */
    public function delete(int $id): RedirectResponse
    {
        $asset = Asset::ofCompany((int) Auth::user()->company_id)->findOrFail($id);
        $asset->delete();

        return redirect()->route('asset.index', $asset->category->value)
            ->with(['status' => 'success', 'message' => __('Asset deleted successfully')]);
    }
}
