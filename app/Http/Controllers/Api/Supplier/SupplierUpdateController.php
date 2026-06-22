<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Supplier;

use App\Http\Requests\API\SupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class SupplierUpdateController
{
    #[OAT\Put(
        path: '/supplier/{id}',
        summary: 'Update a Supplier',
        security: [['bearerAuth' => []]],
        tags: ['Supplier'],
        parameters: [
            new OAT\Parameter(
                name: 'id',
                in: 'path',
                description: 'Id of Supplier',
                required: true,
                schema: new OAT\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Supplier updated successfully'),
            new OAT\Response(response: 400, description: 'Bad request, please review the parameters'),
        ]
    )]
    public function update(SupplierRequest $request, int $id): JsonResponse
    {
        $supplier = Supplier::where('company_id', Auth::user()->company_id)->find($id);

        if (! $supplier) {
            return response()->json(['error' => 'Supplier not found'], 404);
        }

        $supplier->update($request->validated());

        return response()->json(['message' => 'Supplier updated successfully', 'supplier' => $supplier], 200);
    }
}
