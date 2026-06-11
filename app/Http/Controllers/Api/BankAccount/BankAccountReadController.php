<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\BankAccount;

use App\Models\Bank\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class BankAccountReadController
{
    #[OAT\Get(
        path: '/bank-account/{id}',
        summary: 'Get Bank Account information',
        security: [['bearerAuth' => []]],
        tags: ['BankAccount'],
        parameters: [
            new OAT\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the Bank Account', schema: new OAT\Schema(type: 'integer')),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Bank Account found', content: new OAT\JsonContent(ref: '#/components/schemas/BankAccount')),
            new OAT\Response(response: 404, description: 'Bank Account not found'),
        ]
    )]
    public function read(int $id): JsonResponse
    {
        $account = Account::where('company_id', Auth::user()->company_id)
            ->with(['bank', 'country'])
            ->find($id);

        if (! $account) {
            return response()->json(['message' => 'Bank Account not found'], 404);
        }

        return response()->json(['bank_account' => $account]);
    }
}
