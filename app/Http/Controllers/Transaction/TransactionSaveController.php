<?php

declare(strict_types=1);

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\MainController;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TransactionSaveController extends MainController
{
    public function save(Request $request)
    {
        $isUpdate = $request->filled('id');

        if ($isUpdate) {
            if (Auth::user()->cannot('update accounting')) {
                return redirect('/accounting')->with('error', __('Unauthorized'));
            }
        } elseif (Auth::user()->cannot('create accounting')) {
            return redirect('/accounting')->with('error', __('Unauthorized'));
        }

        $amountRule = Auth::user()->can('update accounting')
            ? ['required', 'numeric']
            : ['required', 'numeric', 'min:0'];

        $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'type' => ['required', 'in:income,expense'],
            'amount' => $amountRule,
            'issue_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'payment_date' => ['nullable', 'date'],
            'status' => ['required', 'in:pending,paid,overdue'],
            'transaction_category_id' => ['nullable', 'integer'],
            'bank_account_id' => ['nullable', 'integer'],
            'bank_card_id' => ['nullable', 'integer'],
            'reference' => ['nullable', 'string', 'max:80'],
            'customer_id' => ['nullable', 'integer'],
            'supplier_id' => ['nullable', 'integer'],
            'notes' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx,xls,xlsx'],
        ]);

        $transaction = empty($request->id)
            ? new Transaction
            : Transaction::where('company_id', Auth::user()->company_id)->findOrFail($request->id);

        $transaction->company_id = Auth::user()->company_id;
        $transaction->fill($request->only([
            'name', 'type', 'amount', 'issue_date', 'due_date', 'payment_date',
            'status', 'transaction_category_id', 'bank_account_id', 'bank_card_id',
            'reference', 'customer_id', 'supplier_id', 'notes',
        ]));

        if ($request->hasFile('attachment')) {
            // Delete old file if replacing
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
