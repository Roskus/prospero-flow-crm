<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Api\ApiController;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class OrderListController extends ApiController
{
    #[OAT\Get(
        path: '/order',
        summary: 'Orders list by company',
        security: [['bearerAuth' => []]],
        tags: ['Order'],
        parameters: [
            new OAT\Parameter(name: 'per_page', description: 'Number of results per page, at most '.self::MAX_ITEMS_PER_PAGE, in: 'query', required: false, schema: new OAT\Schema(type: 'integer', default: self::DEFAULT_ITEMS_PER_PAGE, maximum: self::MAX_ITEMS_PER_PAGE, minimum: 1)),
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Orders list retrieved successfully',
                content: new OAT\JsonContent(
                    properties: [
                        new OAT\Property(property: 'count', description: 'Total number of results', type: 'integer', example: 1),
                        new OAT\Property(property: 'orders', description: 'Results of the page', type: 'array', items: new OAT\Items(ref: '#/components/schemas/Order')),
                    ],
                    type: 'object',
                    allOf: [new OAT\Schema(ref: '#/components/schemas/Pagination')]
                )
            ),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $orders = Order::where('company_id', Auth::user()->company_id)
            ->orderByDesc('id')
            ->paginate($this->itemsPerPage($request));

        return $this->paginatedListResponse('orders', $orders);
    }
}
