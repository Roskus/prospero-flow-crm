<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountUpdateController
{
    public function update(Request $request, int $id)
    {
        $account = Account::find($id);
        $data['account'] = $account;

        return view('account.account', $data);
    }
}
