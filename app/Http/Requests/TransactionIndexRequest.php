<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Transaction;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TransactionIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('read transaction') ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:80'],
            'type' => ['nullable', Rule::in([Transaction::INCOME, Transaction::EXPENSE])],
            'status' => ['nullable', Rule::in([Transaction::PENDING, Transaction::PAID, Transaction::OVERDUE])],
            'transaction_category_id' => ['nullable', 'integer', Rule::exists('transaction_category', 'id')->where('company_id', Auth::user()->company_id)],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'type.in' => __('The selected type is not valid.'),
            'status.in' => __('The selected status is not valid.'),
            'transaction_category_id.exists' => __('The selected category is not valid.'),
            'from.date' => __('The start date is not valid.'),
            'to.date' => __('The end date is not valid.'),
            'to.after_or_equal' => __('The end date must be on or after the start date.'),
        ];
    }
}
