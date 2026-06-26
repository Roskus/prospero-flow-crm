<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Customer;

use App\Http\Requests\CustomerRequest;
use App\Repositories\CustomerRepository;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OAT;

class CustomerCreateController
{
    private CustomerRepository $customerSaveRepository;

    public function __construct(CustomerRepository $customerSaveRepository)
    {
        $this->customerSaveRepository = $customerSaveRepository;
    }

    #[OAT\Post(
        path: '/customer',
        summary: 'Create a Customer',
        security: [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(ref: '#/components/schemas/Customer')
        ),
        tags: ['Customer'],
        responses: [
            new OAT\Response(response: 201, description: 'Customer created successfully'),
            new OAT\Response(response: 403, description: 'Unauthorized'),
        ]
    )]
    public function create(CustomerRequest $request): JsonResponse
    {
        $customer = $this->customerSaveRepository->save($request->validated());

        return response()->json(['customer' => ['id' => $customer->id]], 201);
    }
}
