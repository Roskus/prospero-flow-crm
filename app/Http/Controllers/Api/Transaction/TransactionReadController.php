<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Transaction;

use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OAT;

class TransactionReadController
{
    #[OAT\Get(
        path: '/transaction/{id}',
        summary: 'Get Transaction details',
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
            new OAT\Response(response: 200, description: 'Transaction found'),
            new OAT\Response(response: 404, description: 'Transaction not found'),
        ]
    )]
    public function read(int $id): JsonResponse
    {
        $transaction = Transaction::find($id);

        if (! $transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        return response()->json(['transaction' => $transaction], 200);
    }
}
