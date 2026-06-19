<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\BankAccount;

use App\Http\Requests\BankAccountDeleteRequest;
use App\Models\Bank\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class BankAccountDeleteController
{
    #[OAT\Delete(
        path: '/bank-account/{id}',
        summary: 'Delete a Bank Account',
        security: [['bearerAuth' => []]],
        tags: ['BankAccount'],
        parameters: [
            new OAT\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the Bank Account', schema: new OAT\Schema(type: 'integer')),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Bank Account deleted successfully'),
            new OAT\Response(response: 404, description: 'Bank Account not found'),
        ]
    )]
    public function delete(BankAccountDeleteRequest $request, int $id): JsonResponse
    {
        $account = Account::where('company_id', Auth::user()->company_id)->find($id);

        if (! $account) {
            return response()->json(['message' => 'Bank Account not found'], 404);
        }

        $account->delete();

        return response()->json(['message' => 'Bank Account deleted successfully']);
    }
}
