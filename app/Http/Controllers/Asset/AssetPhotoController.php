<?php

declare(strict_types=1);

namespace App\Http\Controllers\Asset;

use App\Http\Controllers\MainController;
use App\Models\Asset;
use App\Services\AssetService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssetPhotoController extends MainController
{
    /**
     * Photos live on a private disk, so only users of the asset's company can see them.
     */
    public function show(int $id): StreamedResponse
    {
        $asset = Asset::ofCompany((int) Auth::user()->company_id)->findOrFail($id);
        $disk = Storage::disk(AssetService::PHOTO_DISK);

        abort_unless($asset->hasPhoto() && $disk->exists($asset->photo_path), 404);

        return $disk->response($asset->photo_path, null, ['Cache-Control' => 'private, max-age=86400']);
    }
}
