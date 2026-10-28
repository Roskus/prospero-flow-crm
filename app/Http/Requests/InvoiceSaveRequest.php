<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class InvoiceSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('create invoice') ?? false;
    }

    public function rules(): array
    {
        return [
            'order_id' => ['required', 'integer', Rule::exists('order', 'id')->where('company_id', Auth::user()->company_id)->whereNull('deleted_at')],
            'issue_date' => ['required', 'date_format:Y-m-d'],
            'due_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:issue_date'],
            'exchange_rate' => ['nullable', 'numeric', 'gt:0', 'max:9999999999'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'due_date.after_or_equal' => __('The due date cannot be earlier than the issue date.'),
        ];
    }
}
