<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\MainController;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountIndexController extends MainController
{
    public function index(Request $request)
    {
        $account = new Account();
        $data['accounts'] = $account->getAllActiveByCompany(Auth::user()->company_id);

        return view('account.index', $data);
    }
}
