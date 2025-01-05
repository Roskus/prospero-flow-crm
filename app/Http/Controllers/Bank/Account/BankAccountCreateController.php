<?php

declare(strict_types=1);

namespace App\Http\Controllers\Bank\Account;

use App\Models\Bank;
use App\Models\Bank\Account as BankAccount;
use Squire\Models\Country;

class BankAccountCreateController
{
    public function create()
    {
        $bank = new Bank;
        $bank_account = new BankAccount;
        $data['bank'] = $bank->getAll();
        $data['countries'] = Country::all();
        $data['bank_account'] = $bank_account;

        return view('bank.account.bank_account', $data);
    }
}
