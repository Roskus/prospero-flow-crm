<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\BankAccount;

use App\Http\Requests\BankAccountUpdateRequest;
use App\Models\Bank\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class BankAccountUpdateController
{
    #[OAT\Put(
        path: '/bank-account/{id}',
        summary: 'Update a Bank Account',
        security: [['bearerAuth' => []]],
        tags: ['BankAccount'],
        parameters: [
            new OAT\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the Bank Account', schema: new OAT\Schema(type: 'integer')),
        ],
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(ref: '#/components/schemas/BankAccount')
        ),
        responses: [
            new OAT\Response(response: 200, description: 'Bank Account updated successfully', content: new OAT\JsonContent(ref: '#/components/schemas/BankAccount')),
            new OAT\Response(response: 404, description: 'Bank Account not found'),
            new OAT\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(BankAccountUpdateRequest $request, int $id): JsonResponse
    {
        $account = Account::where('company_id', Auth::user()->company_id)->find($id);

        if (! $account) {
            return response()->json(['message' => 'Bank Account not found'], 404);
        }

        $account->update($request->validated());

        return response()->json(['bank_account' => $account]);
    }
}
