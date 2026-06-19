<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Order;

use App\Http\Requests\OrderUpdateRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class OrderUpdateController
{
    #[OAT\Put(
        path: '/order/{id}',
        summary: 'Update an Order',
        security: [['bearerAuth' => []]],
        tags: ['Order'],
        parameters: [
            new OAT\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'Order ID',
                schema: new OAT\Schema(type: 'integer')
            ),
        ],
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(ref: '#/components/schemas/Order')
        ),
        responses: [
            new OAT\Response(response: 200, description: 'Order updated successfully'),
            new OAT\Response(response: 403, description: 'Unauthorized'),
            new OAT\Response(response: 404, description: 'Order not found'),
            new OAT\Response(response: 422, description: 'Validation failed'),
        ]
    )]
    public function update(OrderUpdateRequest $request, int $id): JsonResponse
    {
        $order = Order::find($id);

        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $data = $request->validated();
        $data['updated_by'] = Auth::user()->id;

        $order->update($data);

        return response()->json(['order' => $order], 200);
    }
}
