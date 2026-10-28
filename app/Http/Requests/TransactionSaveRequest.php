<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Helpers\CurrencyHelper;
use App\Http\Requests\Concerns\SanitizesInput;
use App\Http\Requests\Concerns\ValidatesCurrency;
use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TransactionSaveRequest extends FormRequest
{
    use SanitizesInput;
    use ValidatesCurrency;

    public function authorize(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        // Require the 'update accounting' permission to modify an existing transaction
        if ($this->filled('id')) {
            return $user->can('update accounting');
        }

        // Require the 'create accounting' permission to create a new transaction
        return $user->can('create accounting');
    }

    public function rules(): array
    {
        /*
         * Negative amounts (refunds, corrections) can corrupt the accounting balance.
         * Only SuperAdmin and company admins are trusted to enter negative values.
         * All other roles are restricted to positive amounts (min:0).
         */
        $amountRule = (Auth::user()?->hasRole('SuperAdmin') || Auth::user()?->isCompanyAdmin())
            ? ['required', 'numeric', 'min:-'.CurrencyHelper::MAX_AMOUNT, 'max:'.CurrencyHelper::MAX_AMOUNT]
            : ['required', 'numeric', 'min:0', 'max:'.CurrencyHelper::MAX_AMOUNT];

        return [
            'name' => ['required', 'string', 'max:80'],
            'type' => ['required', 'in:income,expense'],
            'amount' => $amountRule,
            'issue_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'payment_date' => ['nullable', 'date'],
            'status' => ['required', 'in:pending,paid,overdue'],
            'transaction_category_id' => [
                'nullable',
                'integer',
                Rule::exists('transaction_category', 'id')->where('company_id', $this->user()?->company_id),
            ],
            'bank_account_id' => [
                'nullable',
                'integer',
                Rule::exists('bank_account', 'id')
                    ->where('company_id', $this->user()?->company_id)
                    ->whereNull('bank_account.deleted_at'),
            ],
            'bank_card_id' => [
                'nullable',
                'integer',
                Rule::exists('bank_card', 'id')
                    ->where('company_id', $this->user()?->company_id)
                    ->whereNull('bank_card.deleted_at'),
            ],
            'payment_method' => ['nullable', 'string', Rule::in(array_keys(Transaction::PAYMENT_METHODS))],
            'currency' => $this->currencyRules(),
            'exchange_rate' => ['nullable', 'numeric', 'gt:0', 'max:9999999999'],
            'reference' => ['nullable', 'string', 'max:80'],
            'customer_id' => [
                'nullable',
                'integer',
                Rule::exists('customer', 'id')
                    ->where('company_id', $this->user()?->company_id)
                    ->whereNull('customer.deleted_at'),
            ],
            'supplier_id' => [
                'nullable',
                'integer',
                Rule::exists('supplier', 'id')
                    ->where('company_id', $this->user()?->company_id)
                    ->whereNull('supplier.deleted_at'),
            ],
            'notes' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx,xls,xlsx'],
        ];
    }
}
