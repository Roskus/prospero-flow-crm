<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Services\SecurityLogger;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class OrderSaveRequest extends FormRequest
{
    public function __construct(private SecurityLogger $securityLogger)
    {
        parent::__construct();
    }

    public function authorize(): bool
    {
        return Auth::user()->can('create order') && Auth::user()->can('update order');
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

    public function withValidator(Validator $validator): void
    {
        if ($this->user()->can('override order price')) {
            return;
        }

        $maxDiscount = (float) config('crm.orders.max_discount_percent');

        $validator->after(function (Validator $validator) use ($maxDiscount): void {
            foreach ($this->input('items', []) as $index => $item) {
                $discount = (float) ($item['discount'] ?? 0);

                if ($discount > $maxDiscount) {
                    $this->securityLogger->log('order_discount_over_limit', [
                        'discount' => $discount,
                        'max_discount_percent' => $maxDiscount,
                        'product_id' => (int) ($item['product_id'] ?? 0),
                    ]);

                    $validator->errors()->add(
                        "items.$index.discount",
                        __('Discount cannot exceed :max% without the price override permission.', ['max' => $maxDiscount])
                    );
                }
            }
        });
    }
}
