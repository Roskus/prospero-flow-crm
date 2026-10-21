<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Supplier;

use App\Http\Requests\API\SupplierRequest;
use App\Models\Supplier;
use App\ValueObjects\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
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
                description: 'Id of Supplier',
                in: 'path',
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

        $data = $request->validated();
        if (array_key_exists('country', $data)) {
            $data['country_id'] = $data['country'];
        }
        $addressData = Arr::only($data, Address::COLUMNS);

        $supplier->fill(Arr::except($data, ['country', ...Address::COLUMNS]));
        if ($addressData !== []) {
            $supplier->address = Address::fromArray([...$supplier->address->toArray(), ...$addressData]);
        }
        $supplier->save();

        return response()->json(['message' => 'Supplier updated successfully', 'supplier' => $supplier], 200);
    }
}
