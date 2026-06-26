<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\BankCard;

use App\Http\Requests\BankCardDeleteRequest;
use App\Models\BankCard;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class BankCardDeleteController
{
    #[OAT\Delete(
        path: '/bank-card/{id}',
        summary: 'Delete a Bank Card',
        security: [['bearerAuth' => []]],
        tags: ['BankCard'],
        parameters: [
            new OAT\Parameter(name: 'id', description: 'Id of the Bank Card', in: 'path', required: true, schema: new OAT\Schema(type: 'integer')),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Bank Card deleted successfully'),
            new OAT\Response(response: 404, description: 'Bank Card not found'),
        ]
    )]
    public function delete(BankCardDeleteRequest $request, int $id): JsonResponse // @SuppressWarnings(S1172) - $request used for validation via FormRequest
    {
        $bankCard = BankCard::where('company_id', Auth::user()->company_id)->find($id);

        if (! $bankCard) {
            return response()->json(['message' => 'Bank Card not found'], 404);
        }

        $bankCard->delete();

        return response()->json(['message' => 'Bank Card deleted successfully']);
    }
}
