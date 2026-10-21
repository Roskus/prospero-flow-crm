<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\AddressRules;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Web form of lead, customer and supplier contacts; access is checked by the route permissions.
 */
class ContactSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('country_id'))) {
            $this->merge(['country_id' => strtolower(trim($this->input('country_id')))]);
        }
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return AddressRules::rules();
    }
}
