<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Brand;

use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller for brand deletion.
 *
 * @group Brands
 */
class BrandDeleteController
{
    /**
     * @OA\Delete (
     *      path="/brand/{id}",
     *      summary="Delete a Brand",
     *      tags={"Brand"},
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the Brand",
     *         required=true,
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(response="200", description="Brand deleted successfully"),
     *      @OA\Response(response="400", description="Bad request, please review the parameters")
     * )
     *
     * Delete a brand by ID.
     *
     * @authenticated
     *
     * @return JsonResponse
     */
    public function delete(Request $request, int $id)
    {
        $brand = Brand::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->first();

        if (! $brand) {
            return response()->json(['message' => 'Brand not found'], 404);
        }

        $brand->delete();

        return response()->json(['message' => 'Brand deleted successfully']);
    }
}
