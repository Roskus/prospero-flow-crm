<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Order;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class OrderListController
{
    #[OAT\Get(
        path: '/order',
        summary: 'Orders list by company',
        security: [['bearerAuth' => []]],
        tags: ['Order'],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Orders list retrieved successfully',
                content: new OAT\JsonContent(ref: '#/components/schemas/Order')
            ),
        ]
    )]
    public function index(Request $request)
    {
        $count = Order::where('company_id', Auth::user()->company_id)->count();
        $orders = Order::where('company_id', Auth::user()->company_id)->get();

        return response()->json(['count' => $count, 'orders' => $orders]);
    }
}
