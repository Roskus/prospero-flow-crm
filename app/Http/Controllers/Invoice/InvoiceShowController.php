<?php

declare(strict_types=1);

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\MainController;
use App\Models\Bank\Account;
use App\Models\Invoice;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class InvoiceShowController extends MainController
{
    public function show(Request $request, int $id): View
    {
        $invoice = Invoice::query()
            ->ofCompany($request->user()->company_id)
            ->with(['items', 'order', 'branch', 'company'])
            ->findOrFail($id);

        $company = $request->user()->company;

        return view('invoice.show', [
            'invoice' => $invoice,
            'payments' => $invoice->payments()->with('bankAccount')->orderBy('payment_date')->orderBy('id')->get(),
            'accounts' => Account::query()->where('company_id', $company->id)->orderBy('account_name')->get(),
            'currencies' => $company->usableCurrencies([$invoice->currency]),
        ]);
    }
}
