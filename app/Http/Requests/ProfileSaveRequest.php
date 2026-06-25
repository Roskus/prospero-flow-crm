<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('user')->ignore(Auth::user())],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'timezone' => ['nullable', 'string', 'timezone'],
            'phone' => ['nullable', 'string', 'max:15'],
            'signature_html' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:3072'],
            'lang' => ['nullable', 'string', 'max:10'],
        ];
    }
}
