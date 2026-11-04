<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ContactUpdateRequest extends ContactRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('update contact') ?? false;
    }

    /**
     * Business rule: the lead only has to belong to the user's company. Any seller can add
     * contacts to any company lead, whoever generated it or is assigned to it, so sales can
     * close every lead. Do not restrict it by seller (AssignedSellerScope).
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'lead_id' => ['sometimes', 'nullable', 'integer', Rule::exists('lead', 'id')->where('company_id', Auth::user()->company_id)],
        ];
    }
}
