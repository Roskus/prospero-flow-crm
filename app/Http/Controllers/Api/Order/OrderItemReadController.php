<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Order;

use App\Models\Order\Item;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OAT;

class OrderItemReadController
{
    #[OAT\Get(
        path: '/order-item/{id}',
        summary: 'Get Order Item details',
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
            new OAT\Response(response: 200, description: 'Item found'),
            new OAT\Response(response: 404, description: 'Item not found'),
        ]
    )]
    public function read(int $id): JsonResponse
    {
        $item = Item::find($id);

        if (! $item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        return response()->json(['item' => $item], 200);
    }
}
