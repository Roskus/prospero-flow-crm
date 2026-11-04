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
            new OAT\Parameter(name: 'per_page', description: 'Number of results per page, at most '.self::MAX_ITEMS_PER_PAGE, in: 'query', required: false, schema: new OAT\Schema(type: 'integer', default: self::DEFAULT_ITEMS_PER_PAGE, maximum: self::MAX_ITEMS_PER_PAGE, minimum: 1)),
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Customer list retrieved successfully',
                content: new OAT\JsonContent(
                    properties: [
                        new OAT\Property(property: 'count', description: 'Total number of results', type: 'integer', example: 1),
                        new OAT\Property(property: 'customers', description: 'Results of the page', type: 'array', items: new OAT\Items(ref: '#/components/schemas/Customer')),
                    ],
                    type: 'object',
                    allOf: [new OAT\Schema(ref: '#/components/schemas/Pagination')]
                )
            ),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $customers = Customer::where('company_id', $this->user->company_id)
            ->paginate($this->itemsPerPage($request));

        return $this->paginatedListResponse('customers', $customers);
    }
}
