<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BankAccountSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:bank,stripe,paypal,mercadopago,other'],
            'account_name' => ['nullable', 'string', 'max:80'],
            'bank_id' => ['nullable', 'integer'],
            'country_id' => ['required', 'string', 'max:2'],
            'currency' => ['required', 'string', 'max:3'],
            'opening_balance' => ['nullable', 'numeric'],
            'iban' => ['nullable', 'string', 'max:34'],
            'account_number' => ['nullable', 'string', 'max:30'],
            'swift' => ['nullable', 'string', 'max:11'],
            'sort_code' => ['nullable', 'string', 'max:8'],
            'cbu' => ['nullable', 'string', 'digits:22'],
            'alias' => ['nullable', 'string', 'max:60', 'regex:/^\S+$/'],
            'bizum' => ['nullable', 'string', 'max:15'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
