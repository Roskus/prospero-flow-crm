<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Order;

use App\Http\Requests\OrderItemCreateRequest;
use App\Models\Order;
use App\Models\Order\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class OrderItemCreateController
{
    #[OAT\Post(
        path: '/order-item',
        summary: 'Add item to Order',
        security: [['bearerAuth' => []]],
        tags: ['OrderItem'],
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(ref: '#/components/schemas/OrderItem')
        ),
        responses: [
            new OAT\Response(response: 201, description: 'Item added to order'),
            new OAT\Response(response: 403, description: 'Unauthorized'),
            new OAT\Response(response: 404, description: 'Order not found'),
            new OAT\Response(response: 422, description: 'Validation failed'),
        ]
    )]
    public function create(OrderItemCreateRequest $request): JsonResponse
    {
        $order = Order::where('company_id', Auth::user()->company_id)
            ->where('order_number', $request->validated()['order_number'])
            ->first();

        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $item = Item::create($request->validated());

        return response()->json(['item' => $item], 201);
    }
}
