<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Order;

use App\Http\Requests\OrderItemDeleteRequest;
use App\Models\Order\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class OrderItemDeleteController
{
    #[OAT\Delete(
        path: '/order-item/{id}',
        summary: 'Delete Order Item',
        security: [['bearerAuth' => []]],
        tags: ['OrderItem'],
        parameters: [
            new OAT\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'Item ID',
                schema: new OAT\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Item deleted'),
            new OAT\Response(response: 403, description: 'Unauthorized'),
            new OAT\Response(response: 404, description: 'Item not found'),
        ]
    )]
    public function delete(OrderItemDeleteRequest $request, int $id): JsonResponse
    {
        $item = Item::whereHas('order', function ($query) {
            $query->where('company_id', Auth::user()->company_id);
        })->find($id);

        if (! $item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        $item->delete();

        return response()->json(['message' => 'Item deleted'], 200);
    }
}
