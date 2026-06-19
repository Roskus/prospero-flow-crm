<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Order;

use App\Http\Requests\OrderItemUpdateRequest;
use App\Models\Order\Item;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OAT;

class OrderItemUpdateController
{
    #[OAT\Put(
        path: '/order-item/{id}',
        summary: 'Update Order Item',
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
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(ref: '#/components/schemas/OrderItem')
        ),
        responses: [
            new OAT\Response(response: 200, description: 'Item updated'),
            new OAT\Response(response: 403, description: 'Unauthorized'),
            new OAT\Response(response: 404, description: 'Item not found'),
            new OAT\Response(response: 422, description: 'Validation failed'),
        ]
    )]
    public function update(OrderItemUpdateRequest $request, int $id): JsonResponse
    {
        $item = Item::find($id);

        if (! $item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        $item->update($request->validated());

        return response()->json(['item' => $item], 200);
    }
}
