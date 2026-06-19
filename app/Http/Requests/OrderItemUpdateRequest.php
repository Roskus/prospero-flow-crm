<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class OrderItemUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('update order') ?? false;
    }

    public function rules(): array
    {
        return [
            'quantity' => ['sometimes', 'integer', 'min:1'],
            'unit_price' => ['sometimes', 'numeric', 'min:0.01'],
            'discount' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'tax' => ['sometimes', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
