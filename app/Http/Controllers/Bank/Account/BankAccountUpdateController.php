<?php

declare(strict_types=1);

namespace App\Http\Controllers\Bank\Account;

use App\Http\Controllers\MainController;
use App\Models\Bank;
use App\Models\Bank\Account as BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Squire\Models\Country;

class BankAccountUpdateController extends MainController
{
    public function update(Request $request, int $id)
    {
        $bankAccount = BankAccount::where('company_id', Auth::user()->company_id)->findOrFail($id);

        return view('bank_account.bank_account', [
            'banks' => Bank::orderBy('country_id')->orderBy('name')->with('country')->get(),
            'countries' => Country::all(),
            'bank_account' => $bankAccount,
        ]);
    }
}
