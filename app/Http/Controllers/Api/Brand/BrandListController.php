<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Brand;

use App\Http\Controllers\Api\ApiController;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BrandListController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/brand",
     *     summary="Brand list by company",
     *     tags={"Brand"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Response(
     *         response="200",
     *         description="Brand list retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Brand")
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $count = Brand::where('company_id', Auth::user()->company_id)->count();
        $brands = Brand::where('company_id', Auth::user()->company_id)->get();

        return response()->json(['count' => $count, 'brands' => $brands]);
    }
}
