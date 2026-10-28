<?php

declare(strict_types=1);

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\MainController;
use App\Http\Requests\InvoiceSaveRequest;
use App\Models\Invoice;
use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceSaveController extends MainController
{
    public function save(InvoiceSaveRequest $request, InvoiceService $invoiceService): RedirectResponse
    {
        $order = Order::where('company_id', $request->user()->company_id)->findOrFail($request->integer('order_id'));

        $registersPayment = $request->boolean('register_payment') && $request->user()->can('create payment');

        $invoice = DB::transaction(function () use ($request, $invoiceService, $order, $registersPayment): Invoice {
            $invoice = $invoiceService->issue($order, $request->safe()->only(['issue_date', 'due_date', 'exchange_rate', 'notes']));

            if ($registersPayment) {
                try {
                    $invoiceService->registerPayment($invoice, [
                        ...collect($request->validated('payment', []))->except('attachment')->all(),
                        'attachment' => $request->file('payment.attachment'),
                    ]);
                } catch (ValidationException $exception) {
                    throw ValidationException::withMessages(
                        collect($exception->errors())->mapWithKeys(fn (array $messages, string $key): array => ['payment.'.$key => $messages])->all()
                    );
                }
            }

            return $invoice;
        });

        return redirect()->route('invoice.show', $invoice->id)
            ->with(['status' => 'success', 'message' => __('Invoice :number created', ['number' => $invoice->fullNumber()])]);
    }
}
