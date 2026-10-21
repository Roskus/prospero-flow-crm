<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\AddressRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Partial update: only the fields sent are validated and changed.
 */
class CustomerUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('update customer') ?? false;
    }

    protected function prepareForValidation(): void
    {
        foreach (['phone', 'mobile'] as $field) {
            if ($this->filled($field)) {
                $this->merge([$field => preg_replace('/\D/', '', (string) $this->input($field))]);
            }
        }

        if (is_string($this->input('country_id'))) {
            $this->merge(['country_id' => strtolower(trim($this->input('country_id')))]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:254'],
            'phone' => ['nullable', 'string', 'max_digits:15'],
            'mobile' => ['nullable', 'string', 'max_digits:15'],
            'business_name' => ['nullable', 'string', 'max:120'],
            ...AddressRules::rules(),
            'country_id' => ['sometimes', ...AddressRules::rules(true)['country_id']],
        ];
    }
}
