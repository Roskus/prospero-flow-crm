<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Product;

use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

class ProductReadController
{
    /**
     * @OA\Get(
     *     path="/product/{id}",
     *     summary="Get Product information",
     *     tags={"Product"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of Product",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Product found",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(response="404", description="Product not found")
     * )
     * @return \Illuminate\Http\JsonResponse
     */
    public function read(Request $request, int $id)
    {
        $product = null;
        try {
            $product = Product::where('company_id', Auth::user()->company_id)->where('id', $id)->first();
            $status = 200;
        } catch (ModelNotFoundException $e) {
            $status = 404;
        }

        return response()->json(['product' => $product], $status);
    }
}
