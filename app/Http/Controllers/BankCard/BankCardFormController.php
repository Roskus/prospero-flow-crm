<?php

declare(strict_types=1);

namespace App\Http\Controllers\BankCard;

use App\Models\Bank\Account as BankAccount;
use App\Models\BankCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankCardFormController
{
    public function create(Request $request, int $bankAccountId)
    {
        $bankAccount = BankAccount::where('company_id', Auth::user()->company_id)
            ->findOrFail($bankAccountId);

        return view('bank_card.bank_card', [
            'card' => new BankCard,
            'bank_account' => $bankAccount,
        ]);
    }

    public function edit(Request $request, int $id)
    {
        $card = BankCard::where('company_id', Auth::user()->company_id)
            ->findOrFail($id);

        $bankAccount = BankAccount::where('company_id', Auth::user()->company_id)
            ->findOrFail($card->bank_account_id);

        return view('bank_card.bank_card', [
            'card' => $card,
            'bank_account' => $bankAccount,
        ]);
    }
}
