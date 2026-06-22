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
        if (Auth::user()->cannot('delete accounting')) {
            return redirect()->back()->with('error', __('Unauthorized'));
        }

        $transaction = Transaction::where('company_id', Auth::user()->company_id)->find($id);

        if ($transaction) {
            $transaction->delete();
        }

        return redirect()->back();
    }
}
