<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Order;

use App\Http\Requests\OrderItemCreateRequest;
use App\Models\Order;
use App\Models\Order\Item;
use App\Models\Product;
use App\Services\SecurityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class OrderItemCreateController
{
    public function __construct(private SecurityLogger $securityLogger) {}

    #[OAT\Post(
        path: '/order-item',
        summary: 'Add item to Order',
        security: [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(ref: '#/components/schemas/OrderItem')
        ),
        tags: ['OrderItem'],
        responses: [
            new OAT\Response(response: 201, description: 'Item added to order'),
            new OAT\Response(response: 403, description: 'Unauthorized'),
            new OAT\Response(response: 404, description: 'Order not found'),
            new OAT\Response(response: 422, description: 'Validation failed'),
        ]
    )]
    public function create(OrderItemCreateRequest $request): JsonResponse
    {
        $companyId = Auth::user()->company_id;
        $order = Order::where('company_id', $companyId)
            ->where('order_number', $request->validated()['order_number'])
            ->first();

        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $product = Product::where('company_id', $companyId)
            ->find($request->validated()['product_id']);

        if (! $product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $data = $request->validated();
        $data['order_id'] = $order->id;

        if (Auth::user()->hasPermissionTo('override order price', 'web')) {
            $this->securityLogger->log('order_price_override', [
                'order_id' => $order->id,
                'product_id' => $product->id,
                'catalogue_price' => (float) $product->price,
                'overridden_price' => (float) $data['unit_price'],
                'quantity' => (int) $data['quantity'],
            ]);
        } else {
            $data['unit_price'] = (float) $product->price;
        }

        $item = Item::create($data);

        return response()->json(['item' => $item], 201);
    }
}
