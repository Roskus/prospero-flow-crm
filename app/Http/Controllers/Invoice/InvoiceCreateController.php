<?php

declare(strict_types=1);

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\MainController;
use App\Models\Bank\Account;
use App\Models\Order;
use App\Services\ExchangeRateService;
use App\Services\InvoiceService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class InvoiceCreateController extends MainController
{
    public function create(Request $request, int $order_number, ExchangeRateService $exchangeRateService): View|RedirectResponse
    {
        $order = Order::where('company_id', $request->user()->company_id)
            ->where('order_number', $order_number)
            ->firstOrFail();

        if (! $order->canBeInvoiced()) {
            return redirect('order')->with(['status' => 'error', 'message' => __('Only confirmed orders without an active invoice can be invoiced')]);
        }

        $company = $request->user()->company;

        try {
            $exchangeRate = $exchangeRateService->rate($company, $order->currency);
        } catch (ValidationException) {
            $exchangeRate = '';
        }

        $invoiceTotal = $order->items->reduce(
            fn (string $total, $item): string => bcadd($total, InvoiceService::lineTotals((string) $item->quantity, (string) $item->unit_price, (string) ($item->discount ?? 0), (string) ($item->tax ?? 0))['total'], 2),
            '0'
        );

        return view('invoice.invoice', [
            'invoiceTotal' => $invoiceTotal,
            'accounts' => Account::query()->where('company_id', $company->id)->orderBy('account_name')->get(),
            'currencies' => $company->usableCurrencies([$order->currency]),
            'order' => $order,
            'companyCurrency' => strtoupper((string) $company->currency),
            'exchangeRate' => $exchangeRate,
            'issueDate' => today()->toDateString(),
            'dueDate' => today()->addDays(InvoiceService::DEFAULT_DUE_DAYS)->toDateString(),
        ]);
    }
}
