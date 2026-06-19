<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Supplier;

use App\Http\Requests\SupplierDeleteRequest;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class SupplierDeleteController
{
    #[OAT\Delete(
        path: '/supplier/{id}',
        summary: 'Delete a Supplier',
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
            new OAT\Response(response: 200, description: 'Supplier deleted successfully'),
            new OAT\Response(response: 422, description: 'Validation failed'),
        ]
    )]
    public function delete(SupplierDeleteRequest $request, int $id): JsonResponse
    {
        $supplier = Supplier::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->first();

        if (! $supplier) {
            return response()->json(['message' => 'Supplier not found'], 404);
        }

        $supplier->delete();

        return response()->json(['message' => 'Supplier deleted successfully']);
    }
}
