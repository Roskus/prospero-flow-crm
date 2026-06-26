<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\BankCard;

use App\Http\Requests\BankCardUpdateRequest;
use App\Models\BankCard;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class BankCardUpdateController
{
    #[OAT\Put(
        path: '/bank-card/{id}',
        summary: 'Update a Bank Card',
        security: [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(ref: '#/components/schemas/BankCard')
        ),
        tags: ['BankCard'],
        parameters: [
            new OAT\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the Bank Card', schema: new OAT\Schema(type: 'integer')),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Bank Card updated successfully', content: new OAT\JsonContent(ref: '#/components/schemas/BankCard')),
            new OAT\Response(response: 404, description: 'Bank Card not found'),
            new OAT\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(BankCardUpdateRequest $request, int $id): JsonResponse
    {
        $bankCard = BankCard::where('company_id', Auth::user()->company_id)->find($id);

        if (! $bankCard) {
            return response()->json(['message' => 'Bank Card not found'], 404);
        }

        $bankCard->update($request->validated());

        return response()->json(['bank_card' => $bankCard]);
    }
}
