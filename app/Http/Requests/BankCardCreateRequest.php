<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BankCardCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('create accounting') ?? false;
    }

    public function rules(): array
    {
        return [
            'bank_account_id' => ['required', 'integer', 'exists:bank_account,id'],
            'type' => ['required', 'string', 'in:debit,credit'],
            'network' => ['required', 'string', 'in:visa,mastercard,amex,other'],
            'cardholder_name' => ['required', 'string', 'max:80'],
            'number' => ['required', 'string', 'regex:/^\d{13,19}$/'],
            'cvv' => ['nullable', 'string', 'max:4'],
            'expires_month' => ['required', 'integer', 'between:1,12'],
            'expires_year' => ['required', 'integer', 'min:2020'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
