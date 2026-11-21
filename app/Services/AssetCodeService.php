<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AssetType;
use App\Models\Asset;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Picqer\Barcode\BarcodeGeneratorSVG;

/**
 * Asset codes that work as Code 128 barcodes and QR codes on printed labels.
 */
class AssetCodeService
{
    /**
     * Uppercase letters, digits and hyphens: readable by every barcode scanner keyboard layout.
     */
    const string CODE_PATTERN = '/^[A-Z0-9-]+$/';

    const int SEQUENCE_DIGITS = 5;

    /**
     * Next free code for the type, e.g. MOB-00012. Deleted assets count too, so a code
     * printed on an old label never points to a different asset.
     */
    public function nextCode(int $companyId, AssetType $type): string
    {
        $prefix = $type->codePrefix().'-';

        $max = Asset::withTrashed()
            ->ofCompany($companyId)
            ->where('code', 'like', $prefix.'%')
            ->pluck('code')
            ->map(fn (string $code): string => substr($code, strlen($prefix)))
            ->filter(fn (string $sequence): bool => ctype_digit($sequence))
            ->map(fn (string $sequence): int => (int) $sequence)
            ->max() ?? 0;

        return $prefix.str_pad((string) ($max + 1), self::SEQUENCE_DIGITS, '0', STR_PAD_LEFT);
    }

    /**
     * The code typed or scanned in a search box: a plain code or the URL encoded in a label QR.
     */
    public function codeFromScan(string $search): string
    {
        $search = trim($search);

        if (preg_match('#/inventory/code/([A-Za-z0-9-]+)/?$#', $search, $matches) === 1) {
            $search = $matches[1];
        }

        return strtoupper($search);
    }

    /**
     * URL encoded in the QR: opens the asset when scanned with a phone.
     */
    public function qrContent(Asset $asset): string
    {
        return route('asset.code', $asset->code);
    }

    public function barcodeSvg(string $code): string
    {
        return (new BarcodeGeneratorSVG)->getBarcode($code, BarcodeGeneratorSVG::TYPE_CODE_128, 2, 60);
    }

    public function qrSvg(string $content, int $size = 300): string
    {
        $svg = (new Writer(new ImageRenderer(new RendererStyle($size, 1), new SvgImageBackEnd)))->writeString($content);

        return (string) preg_replace('/^<\?xml[^>]*>\s*/', '', $svg);
    }
}
