<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BankCardUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('update accounting') ?? false;
    }

    public function rules(): array
    {
        return [
            'bank_account_id' => ['sometimes', 'integer', 'exists:bank_account,id'],
            'type' => ['sometimes', 'string', 'in:debit,credit'],
            'network' => ['sometimes', 'string', 'in:visa,mastercard,amex,other'],
            'cardholder_name' => ['sometimes', 'string', 'max:80'],
            'number' => ['sometimes', 'string', 'regex:/^\d{13,19}$/'],
            'cvv' => ['nullable', 'string', 'max:4'],
            'expires_month' => ['sometimes', 'integer', 'between:1,12'],
            'expires_year' => ['sometimes', 'integer', 'min:2020'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
