<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\AddressRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()?->can('create contact') ?? false;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('country_id'))) {
            $this->merge(['country_id' => strtolower(trim($this->input('country_id')))]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Business rule: the lead only has to belong to the user's company. Any seller can add
     * contacts to any company lead, whoever generated it or is assigned to it, so sales can
     * close every lead. Do not restrict it by seller (AssignedSellerScope).
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ...AddressRules::rules(),
            'contact_first_name' => ['required', 'max:50'],
            'lead_id' => ['required_without:customer_id', 'nullable', 'integer', Rule::exists('lead', 'id')->where('company_id', Auth::user()->company_id)],
            'customer_id' => ['nullable', 'integer', Rule::exists('customer', 'id')->where('company_id', Auth::user()->company_id)],
            'contact_email' => ['nullable', 'email', 'max:254'],
            'contact_phone' => ['nullable', 'max:15'],
            'contact_last_name' => ['nullable', 'max:255'],
            'contact_extension' => ['nullable', 'max:6'],
            'contact_mobile' => ['nullable', 'max:15'],
            'contact_job_title' => ['nullable', 'max:80'],
            'contact_linkedin' => ['nullable', 'url', 'max:255'],
            'contact_twitter' => ['nullable', 'max:255'],
            'contact_notes' => ['nullable', 'string'],
        ];
    }
}
