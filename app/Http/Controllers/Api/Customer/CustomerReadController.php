<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Customer;

use App\Models\Customer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class CustomerReadController
{
    #[OAT\Get(
        path: '/customer/{id}',
        summary: 'Get Customer information',
        security: [['bearerAuth' => []]],
        tags: ['Customer'],
        parameters: [
            new OAT\Parameter(name: 'id', description: 'Id of the Customer', in: 'path', required: true, schema: new OAT\Schema(type: 'integer')),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Customer found', content: new OAT\JsonContent(ref: '#/components/schemas/Customer')),
            new OAT\Response(response: 404, description: 'Customer not found'),
        ]
    )]
    public function read(Request $request, int $id): JsonResponse
    {
        $customer = null;
        try {
            $customer = Customer::where('company_id', Auth::user()->company_id)->where('id', $id)->firstOrFail();
            $status = 200;
        } catch (ModelNotFoundException $e) {
            $status = 404;
        }

        return response()->json(['customer' => $customer], $status);
    }
}
