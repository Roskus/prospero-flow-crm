<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Requests\TransactionUpdateRequest;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class TransactionUpdateController
{
    #[OAT\Put(
        path: '/transaction/{id}',
        summary: 'Update a Transaction',
        security: [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(ref: '#/components/schemas/Transaction')
        ),
        tags: ['Transaction'],
        parameters: [
            new OAT\Parameter(
                name: 'id',
                description: 'Transaction ID',
                in: 'path',
                required: true,
                schema: new OAT\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Transaction updated successfully'),
            new OAT\Response(response: 403, description: 'Unauthorized'),
            new OAT\Response(response: 404, description: 'Transaction not found'),
            new OAT\Response(response: 422, description: 'Validation failed'),
        ]
    )]
    public function update(TransactionUpdateRequest $request, int $id): JsonResponse
    {
        $transaction = Transaction::where('company_id', Auth::user()->company_id)->find($id);

        if (! $transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $transaction->update($request->validated());

        return response()->json(['transaction' => $transaction], 200);
    }
}
