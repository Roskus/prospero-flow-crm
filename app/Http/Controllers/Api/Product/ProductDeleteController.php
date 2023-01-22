<?php

namespace App\Http\Controllers\Api\Product;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductDeleteController
{
    /**
     * @OA\Delete (
     *      path="/product/{id}",
     *      summary="Delete a Product",
     *      tags={"Product"},
     *      security={{"bearerAuth": {} }},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of Product",
     *         required=true,
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(response="200", description="Product deleted successfully"),
     *      @OA\Response(response="400", description="Bad request, please review the parameters")
     * )
     *
     * @param  Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function delete(Request $request, int $id)
    {
        $product = Product::find($id)->where('company_id', Auth::user()->company_id);
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
