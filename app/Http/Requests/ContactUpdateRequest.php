<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ContactUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('update contact') ?? false;
    }

    public function rules(): array
    {
        return [
            'contact_first_name' => ['required', 'max:50'],
            'contact_email' => ['sometimes', 'email', 'max:254'],
            'contact_phone' => ['sometimes', 'max:15'],
        ];
    }
}
