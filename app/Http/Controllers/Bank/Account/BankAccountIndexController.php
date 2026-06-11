<?php

declare(strict_types=1);

namespace App\Http\Controllers\Bank\Account;

use App\Models\Bank\Account as BankAccount;
use Illuminate\Support\Facades\Auth;

class BankAccountIndexController
{
    public function index()
    {
        $bankAccounts = BankAccount::where('company_id', Auth::user()->company_id)
            ->with(['bank', 'country'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('bank_account.index', ['bank_accounts' => $bankAccounts]);
    }
}
