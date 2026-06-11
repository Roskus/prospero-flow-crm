<?php

declare(strict_types=1);

namespace App\Http\Controllers\Transaction;

use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TransactionAttachmentDeleteController
{
    public function delete(Request $request, int $id): RedirectResponse
    {
        $transaction = Transaction::where('company_id', Auth::user()->company_id)->findOrFail($id);

        if ($transaction->attachment) {
            Storage::disk('public')->delete($transaction->attachment);
            $transaction->attachment = null;
            $transaction->save();
        }

        return redirect("/transaction/edit/{$id}");
    }
}
