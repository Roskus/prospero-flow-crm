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

    /**
     * Save accounts
     * @param Request $request HTTP request
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

    public function delete(Request $request, int $id)
    {

    }
}
