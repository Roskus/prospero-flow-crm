<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Order;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller for order deletion.
 *
 * @group Orders
 */
class OrderDeleteController
{
    /**
     * @OA\Delete (
     *      path="/order/{id}",
     *      summary="Delete an Order",
     *      tags={"Order"},
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the Order",
     *         required=true,
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(response="200", description="Order deleted successfully"),
     *      @OA\Response(response="400", description="Bad request, please review the parameters")
     * )
     *
     * Delete an order by ID.
     *
     * @authenticated
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function delete(Request $request, int $id)
    {
        $order = Order::find($id)->where('user_id', Auth::id())->get();
        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }
}
