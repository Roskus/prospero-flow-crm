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
