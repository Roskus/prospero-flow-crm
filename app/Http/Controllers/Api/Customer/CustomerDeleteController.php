<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Customer;

use App\Http\Requests\CustomerDeleteRequest;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class CustomerDeleteController
{
    #[OAT\Delete(
        path: '/customer/{id}',
        summary: 'Delete a Customer',
        security: [['bearerAuth' => []]],
        tags: ['Customer'],
        parameters: [
            new OAT\Parameter(name: 'id', in: 'path', required: true, description: 'ID of the Customer', schema: new OAT\Schema(type: 'integer')),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Customer deleted successfully'),
            new OAT\Response(response: 403, description: 'Unauthorized'),
            new OAT\Response(response: 404, description: 'Customer not found'),
        ]
    )]
    public function delete(CustomerDeleteRequest $request, int $id): JsonResponse
    {
        $customer = Customer::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->first();

        if (! $customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $customer->delete();

        return response()->json(['message' => 'Customer deleted successfully']);
    }
}
