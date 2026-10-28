<?php

declare(strict_types=1);

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\MainController;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentDeleteController extends MainController
{
    /**
     * Deletes a payment of the user's company; the transaction observer refreshes the paid amount and status
     * of its invoice, so an invoice left without payments can be cancelled.
     */
    public function delete(Request $request, int $id): RedirectResponse
    {
        $payment = Transaction::query()
            ->where('company_id', $request->user()->company_id)
            ->whereNotNull('invoice_id')
            ->findOrFail($id);

        $payment->delete();

        return redirect()->route('invoice.show', $payment->invoice_id)
            ->with(['status' => 'success', 'message' => __('Payment deleted')]);
    }
}
