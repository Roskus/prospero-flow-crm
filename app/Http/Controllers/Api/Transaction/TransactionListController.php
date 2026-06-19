<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Transaction;

use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class TransactionListController
{
    #[OAT\Get(
        path: '/transaction',
        summary: 'List Transactions',
        security: [['bearerAuth' => []]],
        tags: ['Transaction'],
        responses: [
            new OAT\Response(response: 200, description: 'List of transactions'),
        ]
    )]
    public function index(): JsonResponse
    {
        $transactions = Transaction::where('company_id', Auth::user()->company_id)
            ->orderByDesc('issue_date')
            ->paginate(20);

        return response()->json($transactions, 200);
    }
}
