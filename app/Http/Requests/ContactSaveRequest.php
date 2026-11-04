<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\AddressRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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
     * Business rule: the lead only has to belong to the user's company. Any seller can add
     * contacts to any company lead, whoever generated it or is assigned to it, so sales can
     * close every lead. Do not restrict it by seller (AssignedSellerScope).
     *
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            ...AddressRules::rules(),
            'lead_id' => ['nullable', 'integer', Rule::exists('lead', 'id')->where('company_id', Auth::user()->company_id)],
            'customer_id' => ['nullable', 'integer', Rule::exists('customer', 'id')->where('company_id', Auth::user()->company_id)],
        ];
    }
}
