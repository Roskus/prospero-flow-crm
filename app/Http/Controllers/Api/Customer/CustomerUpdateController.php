<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Customer;

use App\Http\Requests\CustomerUpdateRequest;
use App\Models\Customer;
use App\ValueObjects\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class CustomerUpdateController
{
    #[OAT\Put(
        path: '/customer/{id}',
        summary: 'Update a Customer',
        security: [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(ref: '#/components/schemas/Customer')
        ),
        tags: ['Customer'],
        parameters: [
            new OAT\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the Customer', schema: new OAT\Schema(type: 'integer')),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Customer updated successfully'),
            new OAT\Response(response: 422, description: 'Validation failed'),
            new OAT\Response(response: 404, description: 'Customer not found'),
        ]
    )]
    public function update(CustomerUpdateRequest $request, int $id): JsonResponse
    {
        $customer = Customer::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->first();

        if (! $customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $data = $request->validated();
        $customer->fill(Arr::except($data, Address::COLUMNS));
        $customer->address = $customer->address->merge($data);
        $customer->save();

        return response()->json(['customer' => $customer->toArray()], 200);
    }
}
