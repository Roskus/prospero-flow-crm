<?php

declare(strict_types=1);

namespace App\Http\Controllers\Transaction;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionDeleteController
{
    public function delete(Request $request, int $id)
    {
        if (Auth::user()->cannot('transaction delete')) {
            return redirect()->back()->with('error', 'You do not have permission to delete transactions.');
        }

        $transaction = Transaction::find($id);
        if ($transaction) {
            $transaction->delete();
        }

        return redirect()->back();
    }
}
