<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Services\SecurityLogger;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class OrderItemUpdateRequest extends FormRequest
{
    public function __construct(private SecurityLogger $securityLogger)
    {
        parent::__construct();
    }

    public function authorize(): bool
    {
        return Auth::user()?->hasPermissionTo('update order', 'web') ?? false;
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

    public function withValidator(Validator $validator): void
    {
        if ($this->user()->hasPermissionTo('override order price', 'web')) {
            return;
        }

        $maxDiscount = (float) config('crm.orders.max_discount_percent');

        $validator->after(function (Validator $validator) use ($maxDiscount): void {
            $discount = (float) ($this->input('discount') ?? 0);

            if ($discount > $maxDiscount) {
                $this->securityLogger->log('order_discount_over_limit', [
                    'discount' => $discount,
                    'max_discount_percent' => $maxDiscount,
                    'product_id' => (int) ($this->route('id') ?? 0),
                ]);

                $validator->errors()->add(
                    'discount',
                    __('Discount cannot exceed :max% without the price override permission.', ['max' => $maxDiscount])
                );
            }
        });
    }
}
