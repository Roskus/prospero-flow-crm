<?php

declare(strict_types=1);

namespace App\Http\Controllers\Bank\Account;

use App\Models\Bank\Account as BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankAccountShowController
{
    public function show(Request $request, int $id)
    {
        $bankAccount = BankAccount::where('company_id', Auth::user()->company_id)
            ->with(['bank', 'country', 'cards'])
            ->findOrFail($id);

        $transactions = $bankAccount->transactions()
            ->with(['category', 'customer', 'supplier'])
            ->orderBy('issue_date', 'desc')
            ->get();

        return view('bank_account.show', [
            'bank_account' => $bankAccount,
            'transactions' => $transactions,
            'balance' => $bankAccount->balance(),
        ]);
    }
}
