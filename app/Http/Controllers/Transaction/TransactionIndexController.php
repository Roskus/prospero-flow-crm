<?php

declare(strict_types=1);

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\MainController;
use App\Http\Requests\TransactionIndexRequest;
use App\Models\Transaction;
use App\Models\Transaction\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class TransactionIndexController extends MainController
{
    public function index(TransactionIndexRequest $request): View
    {
        $companyId = (int) Auth::user()->company_id;
        $filters = $request->validated();
        $query = (new Transaction)->searchByCompany($companyId, $filters);

        $transactions = $query->clone()
            ->with(['category', 'customer', 'supplier', 'bankAccount.bank', 'bankCard.bankAccount.bank'])
            ->orderBy('issue_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('transaction.index', [
            'transactions' => $transactions,
            'filters' => $filters,
            'categories' => Category::where('company_id', $companyId)->orderBy('name')->get(),
            'totalIncome' => (float) $query->clone()->where('type', Transaction::INCOME)->sum('amount'),
            'totalExpense' => (float) $query->clone()->where('type', Transaction::EXPENSE)->sum('amount'),
        ]);
    }
}
