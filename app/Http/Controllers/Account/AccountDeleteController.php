<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountDeleteController
{
    public function delete(Request $request, int $id)
    {
        //TODO add security check, add can('account delete')
        $account = Account::find($id);
        $account->delete();

        return redirect()->back();
    }
}
