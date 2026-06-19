<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\BankAccount;

use App\Models\Bank\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class BankAccountListController
{
    #[OAT\Get(
        path: '/bank-account',
        summary: 'Bank account list by company',
        security: [['bearerAuth' => []]],
        tags: ['BankAccount'],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Bank account list retrieved successfully',
                content: new OAT\JsonContent(ref: '#/components/schemas/BankAccount')
            ),
        ]
    )]
    public function index(): JsonResponse
    {
        $accounts = Account::where('company_id', Auth::user()->company_id)
            ->with(['bank', 'country'])
            ->get();

        return response()->json(['count' => $accounts->count(), 'bank_accounts' => $accounts]);
    }
}
