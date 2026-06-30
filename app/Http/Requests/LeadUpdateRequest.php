<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LeadUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('update lead') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:254'],
            'phone' => ['nullable', 'string', 'max:15'],
            'mobile' => ['nullable', 'string', 'max:15'],
            'country_id' => ['nullable', 'string', 'max:2'],
            'business_name' => ['nullable', 'string', 'max:120'],
        ];
    }
}
