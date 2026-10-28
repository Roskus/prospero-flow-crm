<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Helpers\CurrencyHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BankAccountCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('create accounting') ?? false;
    }

    public function rules(): array
    {
        return [
            'bank_id' => ['required', 'integer'],
            'country_id' => ['required', 'string', 'max:2'],
            'currency' => ['required', 'string', 'max:3'],
            'iban' => ['required', 'string', 'max:34'],
            'opening_balance' => ['nullable', 'numeric', 'min:-'.CurrencyHelper::MAX_AMOUNT, 'max:'.CurrencyHelper::MAX_AMOUNT],
            'notes' => ['nullable', 'string'],
            'show_on_invoice' => ['sometimes', 'boolean'],
        ];
    }
}
