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
            'first_name' => ['required', 'max:50'],
            'email' => ['required', 'email', 'max:254'],
            'phone' => ['required', 'max:15'],
            'country_id' => ['required', 'max:2'],
        ];
    }
}
