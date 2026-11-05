<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ProspectListRequest;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OAT;

class CustomerListController extends ApiController
{
    #[OAT\Get(
        path: '/customer',
        summary: 'Customer list by company',
        security: [['bearerAuth' => []]],
        tags: ['Customer'],
        parameters: [
            new OAT\Parameter(ref: '#/components/parameters/Search'),
            new OAT\Parameter(ref: '#/components/parameters/Page'),
            new OAT\Parameter(ref: '#/components/parameters/PerPage'),
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
    public function index(ProspectListRequest $request): JsonResponse
    {
        $customers = Customer::where('company_id', $this->user->company_id)
            ->search($request->validated('search'))
            ->paginate($this->itemsPerPage($request));

        return $this->paginatedListResponse('customers', $customers);
    }
}
