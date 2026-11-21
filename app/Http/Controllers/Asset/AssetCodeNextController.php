<?php

declare(strict_types=1);

namespace App\Http\Controllers\Asset;

use App\Enums\AssetType;
use App\Http\Controllers\MainController;
use App\Http\Requests\AssetCodeNextRequest;
use App\Services\AssetCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AssetCodeNextController extends MainController
{
    public function next(AssetCodeNextRequest $request, AssetCodeService $assetCodeService): JsonResponse
    {
        $code = $assetCodeService->nextCode((int) Auth::user()->company_id, AssetType::from((string) $request->validated('type')));

        return response()->json(['code' => $code]);
    }
}
