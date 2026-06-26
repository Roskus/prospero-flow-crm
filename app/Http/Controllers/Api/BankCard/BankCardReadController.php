<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\BankCard;

use App\Http\Requests\BankCardReadRequest;
use App\Models\BankCard;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class BankCardReadController
{
    #[OAT\Get(
        path: '/bank-card/{id}',
        summary: 'Get Bank Card information',
        security: [['bearerAuth' => []]],
        tags: ['BankCard'],
        parameters: [
            new OAT\Parameter(name: 'id', description: 'Id of the Bank Card', in: 'path', required: true, schema: new OAT\Schema(type: 'integer')),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Bank Card found', content: new OAT\JsonContent(ref: '#/components/schemas/BankCard')),
            new OAT\Response(response: 404, description: 'Bank Card not found'),
        ]
    )]
    public function read(BankCardReadRequest $request, int $id): JsonResponse
    {
        $bankCard = BankCard::where('company_id', Auth::user()->company_id)
            ->with('bankAccount')
            ->find($id);

        if (! $bankCard) {
            return response()->json(['message' => 'Bank Card not found'], 404);
        }

        return response()->json(['bank_card' => $bankCard]);
    }
}
