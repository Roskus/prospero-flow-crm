<?php

declare(strict_types=1);

namespace App\Http\Controllers\Bank\Account;

use App\Models\Bank\Account as BankAccount;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;

class BankAccountSaveController
{
    public function save(Request $request)
    {
        $bank_account = new BankAccount;
        $bank_account->company_id = Auth::user()->company_id;
        $bank_account->bank_id = $request->input('bank_id');
        $bank_account->country_id = $request->input('country_id');
        $bank_account->currency = $request->input('currency');
        $bank_account->iban = $request->input('iban');
        $bank_account->amount = $request->input('amount');
        $bank_account->notes = $request->input('notes');
        $bank_account->updated_at = now();
        $bank_account->save();

        return redirect('/bank-account');
    }
}
