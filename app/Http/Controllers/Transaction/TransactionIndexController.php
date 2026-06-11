<?php

declare(strict_types=1);

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\MainController;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionIndexController extends MainController
{
    public function index(Request $request)
    {
        $companyId = (int) Auth::user()->company_id;
        $transactions = Transaction::where('company_id', $companyId)
            ->with(['category', 'customer', 'supplier', 'bankAccount.bank', 'bankCard.bankAccount.bank'])
            ->orderBy('issue_date', 'desc')
            ->get();

        return view('transaction.index', [
            'transactions' => $transactions,
            'totalIncome' => $transactions->where('type', Transaction::INCOME)->sum('amount'),
            'totalExpense' => $transactions->where('type', Transaction::EXPENSE)->sum('amount'),
        ]);
    }
}
