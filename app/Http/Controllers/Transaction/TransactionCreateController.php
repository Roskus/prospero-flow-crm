<?php

declare(strict_types=1);

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\MainController;
use App\Models\Transaction;
use App\Models\Transaction\Category;
use App\Models\Bank\Account as BankAccount;
use App\Models\BankCard;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionCreateController extends MainController
{
    public function create(Request $request)
    {
        $companyId = (int) Auth::user()->company_id;

        return view('transaction.transaction', [
            'transaction' => new Transaction,
            'categories' => Category::where('company_id', $companyId)->orderBy('name')->get(),
            'bank_accounts' => BankAccount::where('company_id', $companyId)->with('bank')->orderBy('account_name')->get(),
            'bank_cards' => BankCard::where('company_id', $companyId)->orderBy('cardholder_name')->get(),
            'customers' => Customer::where('company_id', $companyId)->orderBy('name')->get(),
            'suppliers' => Supplier::where('company_id', $companyId)->orderBy('name')->get(),
        ]);
    }
}
