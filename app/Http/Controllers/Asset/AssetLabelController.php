<?php

declare(strict_types=1);

namespace App\Http\Controllers\Asset;

use App\Http\Controllers\MainController;
use App\Models\Asset;
use App\Services\AssetCodeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetLabelController extends MainController
{
    const string BARCODE = 'barcode';

    const string QR = 'qr';

    /**
     * Printable label with the asset code as a Code 128 barcode or as a QR that opens the asset.
     */
    public function show(Request $request, int $id, AssetCodeService $assetCodeService): View
    {
        $asset = Asset::ofCompany((int) Auth::user()->company_id)->with('company')->findOrFail($id);
        $format = $request->query('format') === self::QR ? self::QR : self::BARCODE;

        return view('asset.label', [
            'asset' => $asset,
            'format' => $format,
            'symbol' => $format === self::QR
                ? $assetCodeService->qrSvg($assetCodeService->qrContent($asset))
                : $assetCodeService->barcodeSvg($asset->code),
        ]);
    }
}
