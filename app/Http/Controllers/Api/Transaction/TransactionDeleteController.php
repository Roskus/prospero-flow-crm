<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Requests\TransactionDeleteRequest;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OAT;

class TransactionDeleteController
{
    #[OAT\Delete(
        path: '/transaction/{id}',
        summary: 'Delete a Transaction',
        security: [['bearerAuth' => []]],
        tags: ['Transaction'],
        parameters: [
            new OAT\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'Transaction ID',
                schema: new OAT\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Transaction deleted'),
            new OAT\Response(response: 403, description: 'Unauthorized'),
            new OAT\Response(response: 404, description: 'Transaction not found'),
        ]
    )]
    public function delete(TransactionDeleteRequest $request, int $id): JsonResponse
    {
        $transaction = Transaction::find($id);

        if (! $transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $transaction->delete();

        return response()->json(['message' => 'Transaction deleted'], 200);
    }
}
