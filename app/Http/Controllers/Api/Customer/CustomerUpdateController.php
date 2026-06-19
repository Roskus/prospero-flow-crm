<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Customer;

use App\Http\Requests\CustomerUpdateRequest;
use App\Repositories\CustomerRepository;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OAT;

class CustomerUpdateController
{
    private CustomerRepository $customerSaveRepository;

    public function __construct(CustomerRepository $customerSaveRepository)
    {
        $this->customerSaveRepository = $customerSaveRepository;
    }

    #[OAT\Put(
        path: '/customer/{id}',
        summary: 'Update a Customer',
        security: [['bearerAuth' => []]],
        tags: ['Customer'],
        parameters: [
            new OAT\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the Customer', schema: new OAT\Schema(type: 'integer')),
        ],
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(ref: '#/components/schemas/Customer')
        ),
        responses: [
            new OAT\Response(response: 200, description: 'Customer updated successfully'),
            new OAT\Response(response: 422, description: 'Validation failed'),
            new OAT\Response(response: 404, description: 'Customer not found'),
        ]
    )]
    public function update(CustomerUpdateRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $data['id'] = $id;
        $customer = $this->customerSaveRepository->save($data);

        return response()->json(['customer' => $customer->toArray()], 200);
    }
}
