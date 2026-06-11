<?php

declare(strict_types=1);

namespace App\Http\Controllers\Bank\Account;

use App\Models\Bank\Account as BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankAccountSaveController
{
    public function save(Request $request)
    {
        $request->validate([
            'type' => ['required', 'string', 'in:bank,stripe,paypal,mercadopago,other'],
            'account_name' => ['nullable', 'string', 'max:80'],
            'bank_id' => ['nullable', 'integer'],
            'country_id' => ['required', 'string', 'max:2'],
            'currency' => ['required', 'string', 'max:3'],
            'opening_balance' => ['nullable', 'numeric'],
            'iban' => ['nullable', 'string', 'max:34'],
            'account_number' => ['nullable', 'string', 'max:30'],
            'swift' => ['nullable', 'string', 'max:11'],
            'sort_code' => ['nullable', 'string', 'max:8'],
            'cbu' => ['nullable', 'string', 'digits:22'],
            'alias' => ['nullable', 'string', 'max:60', 'regex:/^\S+$/'],
            'bizum' => ['nullable', 'string', 'max:15'],
            'notes' => ['nullable', 'string'],
        ]);

        $bankAccount = empty($request->id)
            ? new BankAccount
            : BankAccount::where('company_id', Auth::user()->company_id)->findOrFail($request->id);

        $bankAccount->company_id = Auth::user()->company_id;
        $bankAccount->fill(array_merge(
            $request->only([
                'type', 'account_name', 'bank_id', 'currency', 'opening_balance',
                'iban', 'account_number', 'swift', 'sort_code', 'cbu', 'alias', 'bizum', 'notes',
            ]),
            ['country_id' => strtolower($request->country_id)]
        ));
        $bankAccount->save();

        return redirect('/bank-account');
    }
}
