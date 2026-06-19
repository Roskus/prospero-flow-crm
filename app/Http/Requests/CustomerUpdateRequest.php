<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CustomerUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('update customer') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'max:120'],
            'email' => ['sometimes', 'email', 'max:254'],
            'phone' => ['sometimes', 'max:15'],
        ];
    }
}
