<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class OrderItemCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('create order') ?? false;
    }

    public function rules(): array
    {
        return [
            'order_number' => ['required', 'string', 'max:50'],
            'product_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0.01'],
            'discount' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'tax' => ['sometimes', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
