<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Helpers\CurrencyHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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
            'product_id' => ['required', 'integer', Rule::exists('product', 'id')->where('company_id', Auth::user()->company_id)],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0.01', 'max:'.CurrencyHelper::MAX_AMOUNT],
            'discount' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'tax' => ['sometimes', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
