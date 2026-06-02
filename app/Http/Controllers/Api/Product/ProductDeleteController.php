<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Product;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

class ProductDeleteController
{
    /**
     * @OA\Delete(
     *     path="/product/{id}",
     *     summary="Delete a Product",
     *     tags={"Product"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of Product",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Product deleted successfully"),
     *     @OA\Response(response="400", description="Bad request, please review the parameters")
     * )
     */
    public function delete(Request $request, int $id): JsonResponse
    {
        $product = Product::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->first();

        if (! $product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
