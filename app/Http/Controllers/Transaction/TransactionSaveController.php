<?php

declare(strict_types=1);

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\MainController;
use App\Http\Requests\TransactionSaveRequest;
use App\Models\Bank\Account as BankAccount;
use App\Models\BankCard;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\Transaction\Category as TransactionCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TransactionSaveController extends MainController
{
    public function save(TransactionSaveRequest $request)
    {
        $transaction = empty($request->id)
            ? new Transaction
            : Transaction::where('company_id', Auth::user()->company_id)->findOrFail($request->id);

        $companyId = Auth::user()->company_id;

        $transaction->company_id = $companyId;

        $validated = $request->validated();

        if (! empty($validated['transaction_category_id'])) {
            TransactionCategory::where('company_id', $companyId)->findOrFail($validated['transaction_category_id']);
        }

        if (! empty($validated['bank_account_id'])) {
            BankAccount::where('company_id', $companyId)->findOrFail($validated['bank_account_id']);
        }

        if (! empty($validated['bank_card_id'])) {
            BankCard::where('company_id', $companyId)->findOrFail($validated['bank_card_id']);
        }

        if (! empty($validated['customer_id'])) {
            Customer::where('company_id', $companyId)->findOrFail($validated['customer_id']);
        }

        if (! empty($validated['supplier_id'])) {
            Supplier::where('company_id', $companyId)->findOrFail($validated['supplier_id']);
        }

        $transaction->fill($validated);

        if ($request->hasFile('attachment')) {
            if ($transaction->attachment) {
                Storage::disk('public')->delete($transaction->attachment);
            }
            $transaction->attachment = $request->file('attachment')
                ->store('accounting/'.Auth::user()->company_id, 'public');
        }

        if (empty($request->id)) {
            $transaction->created_at = now();
        } else {
            $transaction->updated_at = now();
        }

        $transaction->save();

        return redirect('accounting');
    }
}
