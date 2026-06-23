<?php

declare(strict_types=1);

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\MainController;
use App\Http\Requests\TransactionSaveRequest;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TransactionSaveController extends MainController
{
    public function save(TransactionSaveRequest $request)
    {
        $transaction = empty($request->id)
            ? new Transaction
            : Transaction::where('company_id', Auth::user()->company_id)->findOrFail($request->id);

        $transaction->company_id = Auth::user()->company_id;
        $transaction->fill($request->validated());

        if ($request->hasFile('attachment')) {
            if ($transaction->attachment) {
                Storage::disk('public')->delete($transaction->attachment);
            }
            $transaction->attachment = $request->file('attachment')
                ->store('accounting/' . Auth::user()->company_id, 'public');
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
