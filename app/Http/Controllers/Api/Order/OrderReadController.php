<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Order;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class OrderReadController
{
    #[OAT\Get(
        path: '/order/{id}',
        summary: 'Get Order details',
        security: [['bearerAuth' => []]],
        tags: ['Order'],
        parameters: [
            new OAT\Parameter(
                name: 'id',
                description: 'Order ID',
                in: 'path',
                required: true,
                schema: new OAT\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Order found'),
            new OAT\Response(response: 404, description: 'Order not found'),
        ]
    )]
    public function read(int $id): JsonResponse
    {
        $order = Order::where('company_id', Auth::user()->company_id)
            ->with('items')
            ->find($id);

        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json(['order' => $order], 200);
    }
}
