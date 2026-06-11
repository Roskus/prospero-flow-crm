<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Api\ApiController;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OAT;

class CustomerListController extends ApiController
{
    #[OAT\Get(
        path: '/customer',
        summary: 'Customer list by company',
        security: [['bearerAuth' => []]],
        tags: ['Customer'],
        parameters: [
            new OAT\Parameter(name: 'per_page', in: 'query', required: false, description: 'Number of results per page', schema: new OAT\Schema(type: 'integer', default: 15)),
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Customer list retrieved successfully',
                content: new OAT\JsonContent(ref: '#/components/schemas/Customer')
            ),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $customers = Customer::where('company_id', $this->user->company_id)
            ->paginate($request->integer('per_page', self::DEFAULT_ITEMS_PER_PAGE));

        return response()->json($customers);
    }
}
