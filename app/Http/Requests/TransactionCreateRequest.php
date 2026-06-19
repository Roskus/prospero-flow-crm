<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TransactionCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('create accounting') ?? false;
    }

    public function rules(): array
    {
        return [
            'issue_date' => ['required', 'date_format:Y-m-d'],
            'due_date' => ['sometimes', 'date_format:Y-m-d', 'after_or_equal:issue_date'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:income,expense'],
            'transaction_category_id' => ['sometimes', 'integer'],
            'bank_account_id' => ['sometimes', 'integer'],
            'bank_card_id' => ['sometimes', 'integer'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'status' => ['sometimes', 'string', 'in:pending,paid,overdue'],
            'reference' => ['sometimes', 'string', 'max:255'],
            'customer_id' => ['sometimes', 'integer'],
            'supplier_id' => ['sometimes', 'integer'],
            'notes' => ['sometimes', 'string'],
        ];
    }
}
