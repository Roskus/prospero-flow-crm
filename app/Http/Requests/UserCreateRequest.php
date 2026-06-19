<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->hasRole('SuperAdmin') ?? false;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'phone' => ['sometimes', 'string', 'max:15'],
            'lang' => ['required', 'string', 'max:2'],
            'timezone' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }
}
