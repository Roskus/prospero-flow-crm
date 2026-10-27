<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Helpers\CurrencyHelper;
use App\Models\Bank\Account;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BankAccountSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', Rule::in(array_keys(Account::TYPES))],
            'account_name' => ['required_if:type,'.Account::TYPE_CASH, 'nullable', 'string', 'max:80'],
            'bank_id' => ['nullable', 'integer'],
            'country_id' => ['required_unless:type,'.Account::TYPE_CASH, 'nullable', 'string', 'max:2'],
            'currency' => ['required', 'string', 'max:3'],
            'opening_balance' => ['nullable', 'numeric', 'min:-'.CurrencyHelper::MAX_AMOUNT, 'max:'.CurrencyHelper::MAX_AMOUNT],
            'iban' => ['nullable', 'string', 'max:34'],
            'account_number' => ['nullable', 'string', 'max:30'],
            'swift' => ['nullable', 'string', 'max:11'],
            'sort_code' => ['nullable', 'string', 'max:8'],
            'cbu' => ['nullable', 'string', 'digits:22'],
            'alias' => ['nullable', 'string', 'max:60', 'regex:/^\S+$/'],
            'bizum' => ['nullable', 'string', 'max:15'],
            'notes' => ['nullable', 'string'],
            'show_on_invoice' => ['sometimes', 'boolean'],
        ];
    }
}
