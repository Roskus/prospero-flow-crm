<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\BankAccount;

use App\Models\Bank\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            new OAT\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bank_id' => ['required', 'integer'],
            'country_id' => ['required', 'string', 'max:2'],
            'currency' => ['required', 'string', 'max:3'],
            'iban' => ['required', 'string', 'max:34'],
            'amount' => ['required', 'numeric'],
            'notes' => ['nullable', 'string'],
        ]);

        $account = Account::create(array_merge($validated, [
            'company_id' => Auth::user()->company_id,
        ]));

        return response()->json(['bank_account' => ['id' => $account->id]], 201);
    }
}
