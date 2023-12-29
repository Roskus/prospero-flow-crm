<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Order;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

class OrderListController
{
    /**
     * @OA\Get(
     *     path="/order",
     *     summary="Orders list by company",
     *     tags={"Order"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Response(
     *         response="200",
     *         description="Orders list retrived successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $count = Order::where('company_id', Auth::user()->company_id)->count();
        $orders = Order::where('company_id', Auth::user()->company_id)->get();

        return response()->json(['count' => $count, 'orders' => $orders]);
    }
}
