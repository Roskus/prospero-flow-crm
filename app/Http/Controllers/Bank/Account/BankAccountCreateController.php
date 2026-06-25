<?php

declare(strict_types=1);

namespace App\Http\Controllers\Bank\Account;

use App\Http\Controllers\MainController;
use App\Models\Bank;
use App\Models\Bank\Account as BankAccount;
use Squire\Models\Country;

class BankAccountCreateController extends MainController
{
    public function create()
    {
        return view('bank_account.bank_account', [
            'banks' => Bank::orderBy('country_id')->orderBy('name')->with('country')->get(),
            'countries' => Country::all(),
            'bank_account' => new BankAccount,
        ]);
    }
}
