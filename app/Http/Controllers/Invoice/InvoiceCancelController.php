<?php

declare(strict_types=1);

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\MainController;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class InvoiceCancelController extends MainController
{
    public function cancel(Request $request, int $id, InvoiceService $invoiceService): RedirectResponse
    {
        $invoice = Invoice::query()->ofCompany($request->user()->company_id)->findOrFail($id);

        try {
            $invoiceService->cancel($invoice);
        } catch (ValidationException $exception) {
            return redirect()->route('invoice.show', $invoice->id)
                ->with(['status' => 'error', 'message' => $exception->validator->errors()->first()]);
        }

        return redirect()->route('invoice.show', $invoice->id)
            ->with(['status' => 'success', 'message' => __('Invoice :number cancelled', ['number' => $invoice->fullNumber()])]);
    }
}
