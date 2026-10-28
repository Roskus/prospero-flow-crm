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

class TransactionUpdateRequest extends FormRequest
{
    use SanitizesInput;
    use ValidatesCurrency;

    public function authorize(): bool
    {
        return Auth::user()?->can('update accounting') ?? false;
    }

    public function rules(): array
    {
        return [
            'issue_date' => ['sometimes', 'date_format:Y-m-d'],
            'due_date' => ['sometimes', 'date_format:Y-m-d', 'after_or_equal:issue_date'],
            'name' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'in:income,expense'],
            'transaction_category_id' => ['sometimes', 'integer'],
            'bank_account_id' => ['sometimes', 'integer', Rule::exists('bank_account', 'id')->where('company_id', Auth::user()->company_id)->whereNull('deleted_at')],
            'bank_card_id' => ['sometimes', 'integer'],
            'amount' => ['sometimes', 'numeric', 'min:0.01', 'max:'.CurrencyHelper::MAX_AMOUNT],
            'status' => ['sometimes', 'string', 'in:pending,paid,overdue'],
            'payment_method' => ['sometimes', 'nullable', 'string', Rule::in(array_keys(Transaction::PAYMENT_METHODS))],
            'currency' => $this->currencyRules('sometimes'),
            'exchange_rate' => ['sometimes', 'numeric', 'gt:0', 'max:9999999999'],
            'reference' => ['sometimes', 'string', 'max:255'],
            'customer_id' => ['sometimes', 'integer'],
            'supplier_id' => ['sometimes', 'integer'],
            'notes' => ['sometimes', 'string'],
        ];
    }
}
