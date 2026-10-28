<?php

declare(strict_types=1);

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\MainController;
use App\Http\Requests\InvoiceSaveRequest;
use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Http\RedirectResponse;

class InvoiceSaveController extends MainController
{
    public function save(InvoiceSaveRequest $request, InvoiceService $invoiceService): RedirectResponse
    {
        $order = Order::where('company_id', $request->user()->company_id)->findOrFail($request->integer('order_id'));

        $invoice = $invoiceService->issue($order, $request->safe()->only(['issue_date', 'due_date', 'exchange_rate', 'notes']));

        return redirect()->route('invoice.show', $invoice->id)
            ->with(['status' => 'success', 'message' => __('Invoice :number created', ['number' => $invoice->fullNumber()])]);
    }
}
