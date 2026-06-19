<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CompanyCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->hasRole('SuperAdmin') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'max:120'],
            'country_id' => ['required', 'string', 'max:2'],
            'email' => ['sometimes', 'email', 'max:254'],
        ];
    }
}
