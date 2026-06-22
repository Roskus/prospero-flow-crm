<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\BankAccount;

use App\Http\Requests\BankAccountCreateRequest;
use App\Models\Bank\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class BankAccountCreateController
{
    #[OAT\Post(
        path: '/bank-account',
        summary: 'Create a Bank Account',
        security: [['bearerAuth' => []]],
        tags: ['BankAccount'],
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(ref: '#/components/schemas/BankAccount')
        ),
        responses: [
            new OAT\Response(response: 201, description: 'Bank Account created successfully'),
            new OAT\Response(response: 403, description: 'Unauthorized'),
            new OAT\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function create(BankAccountCreateRequest $request): JsonResponse
    {
        $account = Account::create(array_merge($request->validated(), [
            'company_id' => Auth::user()->company_id,
        ]));

        return response()->json(['bank_account' => ['id' => $account->id]], 201);
    }
}
