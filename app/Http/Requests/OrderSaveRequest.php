<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class OrderSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('create order') && Auth::user()?->can('update order');
    }

    public function rules(): array
    {
        return [
            'id' => ['sometimes', 'integer'],
            'customer_id' => ['required', 'integer'],
            'seller_id' => ['sometimes', 'integer'],
            'currency' => ['sometimes', 'string', 'max:3'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'status' => ['sometimes', 'integer', 'in:0,1,2,3'],
            'items' => ['nullable', 'array'],
            'items.*.product_id' => ['required_with:items', 'integer'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
            'items.*.price' => ['required_with:items', 'numeric', 'min:0'],
            'items.*.discount' => ['sometimes', 'numeric', 'min:0'],
            'items.*.tax' => ['sometimes', 'numeric', 'min:0'],
        ];
    }
}
