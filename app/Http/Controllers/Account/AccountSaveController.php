<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\MainController;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountSaveController extends MainController
{
    public function save(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'numeric'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'payment_date' => ['nullable', 'date'],
            'status' => ['required', 'in:pending,paid,overdue'],
            'account_category_id' => ['nullable', 'integer'],
            'reference' => ['nullable', 'string', 'max:80'],
            'customer_id' => ['nullable', 'integer'],
            'supplier_id' => ['nullable', 'integer'],
            'notes' => ['nullable', 'string'],
        ]);

        $account = empty($request->id) ? new Account : Account::find($request->id);
        $account->company_id = Auth::user()->company_id;
        $account->fill($request->only([
            'name', 'type', 'amount', 'issue_date', 'due_date', 'payment_date',
            'status', 'account_category_id', 'reference', 'customer_id', 'supplier_id', 'notes',
        ]));

        if (empty($request->id)) {
            $account->created_at = now();
        } else {
            $account->updated_at = now();
        }

        $account->save();

        return redirect('accounting');
    }
}
