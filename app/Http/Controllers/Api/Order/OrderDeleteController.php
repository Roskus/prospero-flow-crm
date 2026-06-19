<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Order;

use App\Http\Requests\OrderDeleteRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
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
     *      @OA\Response(response="403", description="Unauthorized"),
     *      @OA\Response(response="404", description="Order not found")
     * )
     *
     * Delete an order by ID.
     *
     * @authenticated
     */
    public function delete(OrderDeleteRequest $request, int $id): JsonResponse
    {
        $order = Order::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->first();

        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }
}
