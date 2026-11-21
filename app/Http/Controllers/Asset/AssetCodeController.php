<?php

declare(strict_types=1);

namespace App\Http\Controllers\Asset;

use App\Http\Controllers\MainController;
use App\Models\Asset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AssetCodeController extends MainController
{
    /**
     * Target of the label QR: opens the asset with that code in the user's company.
     */
    public function show(string $code): RedirectResponse
    {
        $asset = Asset::query()->ofCompany((int) Auth::user()->company_id)->where('code', strtoupper($code))->firstOrFail();

        return redirect()->route('asset.show', $asset->id);
    }
}
