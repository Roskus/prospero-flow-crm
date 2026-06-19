<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class OrderUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('update order') ?? false;
    }

    public function rules(): array
    {
        return [
            'amount' => ['sometimes', 'numeric', 'min:0.01'],
            'currency' => ['sometimes', 'string', 'max:3'],
            'status' => ['sometimes', 'integer', 'in:0,1,2,3'],
        ];
    }
}
