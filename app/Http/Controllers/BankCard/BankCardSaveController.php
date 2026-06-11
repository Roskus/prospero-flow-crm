<?php

declare(strict_types=1);

namespace App\Http\Controllers\BankCard;

use App\Models\BankCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankCardSaveController
{
    public function save(Request $request)
    {
        $request->validate([
            'bank_account_id' => ['required', 'integer'],
            'type' => ['required', 'in:debit,credit'],
            'network' => ['required', 'in:visa,mastercard,amex,other'],
            'cardholder_name' => ['required', 'string', 'max:80'],
            'number' => ['required_without:id', 'nullable', 'string', 'min:12', 'max:19'],
            'cvv' => ['nullable', 'string', 'min:3', 'max:4'],
            'expires_month' => ['required', 'integer', 'min:1', 'max:12'],
            'expires_year' => ['required', 'integer', 'min:'.date('Y')],
            'notes' => ['nullable', 'string'],
        ]);

        $card = empty($request->id)
            ? new BankCard
            : BankCard::where('company_id', Auth::user()->company_id)->findOrFail($request->id);

        $card->company_id = Auth::user()->company_id;
        $card->bank_account_id = $request->bank_account_id;
        $card->type = $request->type;
        $card->network = $request->network;
        $card->cardholder_name = $request->cardholder_name;
        $card->expires_month = $request->expires_month;
        $card->expires_year = $request->expires_year;
        $card->notes = $request->notes;

        if ($request->filled('number')) {
            $card->number = $request->number;
        }

        if ($request->filled('cvv')) {
            $card->cvv = $request->cvv;
        }

        $card->save();

        return redirect("/bank-account/show/{$request->bank_account_id}");
    }
}
