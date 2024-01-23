<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\MainController;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountSaveController extends MainController
{
    /**
     * Save accounts
     *
     * @param  Request  $request  HTTP request
     */
    public function save(Request $request)
    {
        $account = (empty($request->id)) ? new Account() : Account::find($request->id);
        $account->company_id = Auth::user()->company_id;
        $account->issue_date = $request->issue_date;
        $account->name = $request->name;
        $account->amount = $request->amount;
        if (empty($request->id)) {
            $account->created_at = now();
        } else {
            $account->updated_at = now();
        }
        $account->save();

        return redirect('accounting');
    }
}
