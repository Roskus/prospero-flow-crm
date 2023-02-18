<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Product;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductListController
{
    /**
     * @OA\Get(
     *     path="/product",
     *     summary="Products list by company",
     *     tags={"Product"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Response(
     *         response="200",
     *         description="Products list retrived successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $count = Product::where('company_id', Auth::user()->company_id)->count();
        $products = Product::where('company_id', Auth::user()->company_id)->get();

        return response()->json(['count' => $count, 'products' => $products]);
    }
}
