<?php

declare(strict_types=1);

namespace App\Http\Controllers\BankCard;

use App\Models\BankCard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankCardDeleteController
{
    public function delete(Request $request, int $id): JsonResponse
    {
        $card = BankCard::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $card->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
