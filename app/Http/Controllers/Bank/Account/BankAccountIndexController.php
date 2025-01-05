<?php

declare(strict_types=1);

namespace App\Http\Controllers\Bank\Account;

use App\Models\Bank\Account as BankAccount;
use Illuminate\Support\Facades\Auth;

class BankAccountIndexController
{
    public function index()
    {
        $bankAccount = new BankAccount;
        $data['bank_accounts'] = $bankAccount->getAllByCompanyId(Auth::user()->company_id);

        return view('bank.account.index');
    }
}
