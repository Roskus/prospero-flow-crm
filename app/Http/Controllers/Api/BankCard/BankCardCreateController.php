<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\BankCard;

use App\Http\Requests\BankCardCreateRequest;
use App\Models\BankCard;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class BankCardCreateController
{
    #[OAT\Post(
        path: '/bank-card',
        summary: 'Create a Bank Card',
        security: [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(ref: '#/components/schemas/BankCard')
        ),
        tags: ['BankCard'],
        responses: [
            new OAT\Response(response: 201, description: 'Bank Card created successfully'),
            new OAT\Response(response: 403, description: 'Unauthorized'),
            new OAT\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function create(BankCardCreateRequest $request): JsonResponse
    {
        $bankCard = BankCard::create(array_merge($request->validated(), [
            'company_id' => Auth::user()->company_id,
        ]));

        return response()->json(['bank_card' => ['id' => $bankCard->id]], 201);
    }
}
