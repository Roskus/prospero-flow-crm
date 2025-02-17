<?php

declare(strict_types=1);

namespace App\Http\Controllers\Bank\Account;

use App\Models\Bank;
use App\Models\Bank\Account as BankAccount;
use Illuminate\Http\Request;
use Squire\Models\Country;

class BankAccountUpdateController
{
    public function update(Request $request, int $id)
    {

        $bank = new Bank;
        $bank_account = BankAccount::find($id);
        $data['bank'] = $bank->getAll();
        $data['countries'] = Country::all();
        $data['bank_account'] = $bank_account;

        return view('bank.account.bank_account', $data);
    }
}
