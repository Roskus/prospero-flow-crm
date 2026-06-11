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
        $companyId = (int) Auth::user()->company_id;
        $accounts = Account::where('company_id', $companyId)
            ->with(['category', 'customer', 'supplier'])
            ->orderBy('issue_date', 'desc')
            ->get();

        return view('account.index', [
            'accounts' => $accounts,
            'totalIncome' => $accounts->where('type', Account::INCOME)->sum('amount'),
            'totalExpense' => $accounts->where('type', Account::EXPENSE)->sum('amount'),
        ]);
    }
}
