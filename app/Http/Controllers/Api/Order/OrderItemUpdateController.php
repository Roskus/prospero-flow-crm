<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Order;

use App\Http\Requests\OrderItemUpdateRequest;
use App\Models\Order\Item;
use App\Services\SecurityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class OrderItemUpdateController
{
    public function __construct(private SecurityLogger $securityLogger) {}

    #[OAT\Put(
        path: '/order-item/{id}',
        summary: 'Update Order Item',
        security: [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(ref: '#/components/schemas/OrderItem')
        ),
        tags: ['OrderItem'],
        parameters: [
            new OAT\Parameter(
                name: 'id',
                description: 'Item ID',
                in: 'path',
                required: true,
                schema: new OAT\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Item updated'),
            new OAT\Response(response: 403, description: 'Unauthorized'),
            new OAT\Response(response: 404, description: 'Item not found'),
            new OAT\Response(response: 422, description: 'Validation failed'),
        ]
    )]
    public function update(OrderItemUpdateRequest $request, int $id): JsonResponse
    {
        $item = Item::whereHas('order', function ($query) {
            $query->where('company_id', Auth::user()->company_id);
        })->find($id);

        if (! $item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        $data = $request->validated();

        if (isset($data['unit_price']) && ! Auth::user()->hasPermissionTo('override order price', 'web')) {
            unset($data['unit_price']);
        } elseif (isset($data['unit_price'])) {
            $this->securityLogger->log('order_price_override', [
                'order_id' => $item->order_id,
                'product_id' => $item->product_id,
                'catalogue_price' => (float) ($item->product->price ?? 0),
                'overridden_price' => (float) $data['unit_price'],
                'quantity' => (int) ($data['quantity'] ?? $item->quantity),
            ]);
        }

        $item->update($data);

        return response()->json(['item' => $item], 200);
    }
}
