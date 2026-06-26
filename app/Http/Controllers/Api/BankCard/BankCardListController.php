<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\BankCard;

use App\Http\Requests\BankCardListRequest;
use App\Models\BankCard;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class BankCardListController
{
    #[OAT\Get(
        path: '/bank-card',
        summary: 'Bank card list by company',
        security: [['bearerAuth' => []]],
        tags: ['BankCard'],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Bank card list retrieved successfully',
                content: new OAT\JsonContent(ref: '#/components/schemas/BankCard')
            ),
        ]
    )]
    public function index(BankCardListRequest $request): JsonResponse
    {
        $bankCards = BankCard::where('company_id', Auth::user()->company_id)
            ->with('bankAccount')
            ->get();

        return response()->json(['count' => $bankCards->count(), 'bank_cards' => $bankCards]);
    }
}
