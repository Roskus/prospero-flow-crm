<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Account;

class AccountController extends MainController
{
    public function index(Request $request)
    {
        $account = new Account();
        $data['accounts'] = $account->getAll();
        return view('account.index', $data);
    }

    public function add(Request $request)
    {

    }

    public function edit(Request $request, int $id = null)
    {

    }

    public function save(Request $request)
    {
        $account = (empty($request->id)) ? new Account() : Account::find($request->id);
        $account->company_id = Auth::user()->company_id;
        $account->name = $request->name;
        $account->amount = $request->amount;
        $account->save();
        return redirect('accounting');
    }

    public function delete(Request $request, int $id)
    {

    }
}
