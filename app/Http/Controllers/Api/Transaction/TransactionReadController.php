<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Requests\TransactionReadRequest;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
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
                description: 'Transaction ID',
                in: 'path',
                required: true,
                schema: new OAT\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Transaction found'),
            new OAT\Response(response: 404, description: 'Transaction not found'),
        ]
    )]
    public function read(TransactionReadRequest $request, int $id): JsonResponse
    {
        $transaction = Transaction::where('company_id', Auth::user()->company_id)->find($id);

        if (! $transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        return response()->json(['transaction' => $transaction], 200);
    }
}
