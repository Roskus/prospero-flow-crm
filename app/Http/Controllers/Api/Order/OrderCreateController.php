<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Order;

use App\Http\Requests\OrderCreateRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class OrderCreateController
{
    #[OAT\Post(
        path: '/order',
        summary: 'Create an Order',
        security: [['bearerAuth' => []]],
        tags: ['Order'],
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(ref: '#/components/schemas/Order')
        ),
        responses: [
            new OAT\Response(response: 201, description: 'Order created successfully'),
            new OAT\Response(response: 403, description: 'Unauthorized'),
            new OAT\Response(response: 422, description: 'Validation failed'),
        ]
    )]
    public function create(OrderCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['company_id'] = Auth::user()->company_id;
        $data['seller_id'] = Auth::user()->id;
        $data['created_by'] = Auth::user()->id;

        $order = Order::create($data);
        $order->load('items');

        return response()->json(['order' => $order], 201);
    }
}
