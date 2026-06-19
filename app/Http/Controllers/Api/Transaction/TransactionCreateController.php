<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Requests\TransactionCreateRequest;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class TransactionCreateController
{
    #[OAT\Post(
        path: '/transaction',
        summary: 'Create a Transaction',
        security: [['bearerAuth' => []]],
        tags: ['Transaction'],
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(ref: '#/components/schemas/Transaction')
        ),
        responses: [
            new OAT\Response(response: 201, description: 'Transaction created successfully'),
            new OAT\Response(response: 403, description: 'Unauthorized'),
            new OAT\Response(response: 422, description: 'Validation failed'),
        ]
    )]
    public function create(TransactionCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['company_id'] = Auth::user()->company_id;

        $transaction = Transaction::create($data);

        return response()->json(['transaction' => $transaction], 201);
    }
}
