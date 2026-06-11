<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Customer;

use App\Repositories\CustomerRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            new OAT\Response(response: 400, description: 'Bad request, please review the parameters'),
            new OAT\Response(response: 404, description: 'Customer not found'),
        ]
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        $status = 400;
        $data = [];
        $valid = $request->validate([
            'first_name' => ['required', 'max:50'],
            'email' => ['required', 'max:254'],
            'phone' => ['required', 'max:15'],
            'country_id' => ['required', 'max:2'],
        ]);

        if ($valid) {
            $params['id'] = $id;
            $params = array_merge($params, $request->all());
            $customer = $this->customerSaveRepository->save($params);
            if ($customer) {
                $status = 200;
                $data['customer'] = $customer->toArray();
            }
        }

        return response()->json($data, $status);
    }
}
