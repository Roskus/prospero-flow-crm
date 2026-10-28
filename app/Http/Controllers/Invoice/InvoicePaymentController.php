<?php

declare(strict_types=1);

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\MainController;
use App\Http\Requests\InvoicePaymentRequest;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\RedirectResponse;

class InvoicePaymentController extends MainController
{
    public function store(InvoicePaymentRequest $request, int $id, InvoiceService $invoiceService): RedirectResponse
    {
        $invoice = Invoice::query()->ofCompany($request->user()->company_id)->findOrFail($id);

        $invoiceService->registerPayment($invoice, [...$request->safe()->except('attachment'), 'attachment' => $request->file('attachment')]);

        return redirect()->route('invoice.show', $invoice->id)
            ->with(['status' => 'success', 'message' => __('Payment registered')]);
    }
}
