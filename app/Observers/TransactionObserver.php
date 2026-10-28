<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Bank\Account;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Services\ExchangeRateService;
use App\Services\InvoiceService;
use Illuminate\Validation\ValidationException;

class TransactionObserver
{
    public function __construct(
        private ExchangeRateService $exchangeRateService,
        private InvoiceService $invoiceService,
    ) {}

    /**
     * Transactions default to the currency of their account, or of the company, and keep the rate of the
     * moment their currency is set. Payments recompute the amount they settle in the invoice currency.
     *
     * @throws ValidationException when the company has no rate for the transaction currency
     */
    public function saving(Transaction $transaction): void
    {
        $company = Company::withTrashed()->find($transaction->company_id);

        if ($company === null) {
            return;
        }

        $companyCurrency = strtoupper((string) $company->currency);
        $accountCurrency = $transaction->bank_account_id
            ? Account::query()->where('company_id', $company->id)->whereKey($transaction->bank_account_id)->value('currency')
            : null;

        $isChosenCurrency = ! empty($transaction->currency) || $transaction->invoice_id !== null;
        $transaction->currency = strtoupper((string) ($transaction->currency ?: $accountCurrency ?: $companyCurrency));

        if ($transaction->currency === $companyCurrency) {
            $transaction->exchange_rate = '1.000000';
        } elseif ($transaction->getAttribute('exchange_rate') === null || ($transaction->isDirty('currency') && ! $transaction->isDirty('exchange_rate'))) {
            $transaction->exchange_rate = $this->rateOf($company, $transaction->currency, $isChosenCurrency);
        }

        if ($transaction->invoice_id !== null && $transaction->isDirty(['invoice_id', 'amount', 'currency', 'exchange_rate'])) {
            $invoice = Invoice::query()->ofCompany($company->id)->findOrFail($transaction->invoice_id);
            $transaction->invoice_amount = InvoiceService::settledAmount($invoice, (string) $transaction->amount, $transaction->currency, (string) $transaction->exchange_rate);
        }
    }

    /**
     * Keeps the paid amount and status of the invoices the transaction pays, before and after the change.
     */
    public function saved(Transaction $transaction): void
    {
        $this->refreshInvoices($transaction, [$transaction->invoice_id, $transaction->getOriginal('invoice_id')]);
    }

    public function deleted(Transaction $transaction): void
    {
        $this->refreshInvoices($transaction, [$transaction->invoice_id]);
    }

    public function restored(Transaction $transaction): void
    {
        $this->refreshInvoices($transaction, [$transaction->invoice_id]);
    }

    public function forceDeleted(Transaction $transaction): void
    {
        $this->refreshInvoices($transaction, [$transaction->invoice_id]);
    }

    /**
     * @param  list<int|string|null>  $invoiceIds
     */
    private function refreshInvoices(Transaction $transaction, array $invoiceIds): void
    {
        $invoiceIds = array_values(array_unique(array_filter($invoiceIds)));

        if ($invoiceIds === []) {
            return;
        }

        Invoice::query()
            ->ofCompany((int) $transaction->company_id)
            ->whereIn('id', $invoiceIds)
            ->get()
            ->each(fn (Invoice $invoice) => $this->invoiceService->refreshPaymentStatus($invoice));
    }

    /**
     * A currency inherited from the account keeps rate 1 when the company has no rate for it, so expenses of
     * accounts in other currencies keep working; a chosen currency or a payment needs the rate.
     *
     * @throws ValidationException
     */
    private function rateOf(Company $company, string $currency, bool $isChosenCurrency): string
    {
        try {
            return $this->exchangeRateService->rate($company, $currency);
        } catch (ValidationException $exception) {
            if ($isChosenCurrency) {
                throw $exception;
            }

            return '1.000000';
        }
    }
}
