<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns;

use App\Helpers\CurrencyHelper;
use App\Models\Transaction;
use Illuminate\Validation\Rule;

/**
 * Rules of the payment of an invoice, alone or as the payment section of the invoice form.
 */
trait ValidatesInvoicePayment
{
    /**
     * @param  list<mixed>  $presence  rules prepended to every field, e.g. to exclude the section
     * @return array<string, list<mixed>>
     */
    protected function paymentRules(string $prefix = '', array $presence = []): array
    {
        $key = fn (string $name): string => $prefix === '' ? $name : $prefix.'.'.$name;

        return [
            $key('payment_method') => [...$presence, 'required', 'string', Rule::in(array_keys(Transaction::PAYMENT_METHODS))],
            $key('amount') => [...$presence, 'required', 'numeric', 'gt:0', 'max:'.CurrencyHelper::MAX_AMOUNT],
            $key('bank_account_id') => [...$presence, 'nullable', 'integer'],
            $key('currency') => [...$presence, 'nullable', 'string', Rule::in(CurrencyHelper::codes())],
            $key('exchange_rate') => [...$presence, 'nullable', 'numeric', 'gt:0', 'max:9999999999'],
            $key('payment_date') => [...$presence, 'nullable', 'date_format:Y-m-d'],
            $key('reference') => [...$presence, 'nullable', 'string', 'max:80'],
            $key('attachment') => [...$presence, 'nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp'],
        ];
    }
}
