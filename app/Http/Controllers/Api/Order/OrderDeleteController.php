<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Order;

use App\Http\Requests\OrderDeleteRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class OrderDeleteController
{
    #[OAT\Delete(
        path: '/order/{id}',
        summary: 'Delete an Order',
        security: [['bearerAuth' => []]],
        tags: ['Order'],
        parameters: [
            new OAT\Parameter(name: 'id', in: 'path', required: true, description: 'ID of the Order', schema: new OAT\Schema(type: 'integer')),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Order deleted successfully'),
            new OAT\Response(response: 403, description: 'Unauthorized'),
            new OAT\Response(response: 404, description: 'Order not found'),
        ]
    )]
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
