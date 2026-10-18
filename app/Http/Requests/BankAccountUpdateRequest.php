<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Helpers\CurrencyHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BankAccountUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('update accounting') ?? false;
    }

    public function rules(): array
    {
        return [
            'bank_id' => ['sometimes', 'integer'],
            'country_id' => ['sometimes', 'string', 'max:2'],
            'currency' => ['sometimes', 'string', 'max:3'],
            'iban' => ['sometimes', 'string', 'max:34'],
            'amount' => ['sometimes', 'numeric', 'min:-'.CurrencyHelper::MAX_AMOUNT, 'max:'.CurrencyHelper::MAX_AMOUNT],
            'notes' => ['nullable', 'string'],
        ];
    }
}
