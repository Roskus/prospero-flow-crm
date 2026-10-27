<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Helpers\CurrencyHelper;
use App\Http\Requests\Concerns\ValidatesCurrency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OrderCreateRequest extends FormRequest
{
    use ValidatesCurrency;

    public function authorize(): bool
    {
        return Auth::user()?->can('create order') ?? false;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer'],
            'branch_id' => ['sometimes', 'integer', Rule::exists('branch', 'id')->where('company_id', Auth::user()->company_id)->whereNull('deleted_at')],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:'.CurrencyHelper::MAX_AMOUNT],
            'currency' => $this->currencyRules(),
            'status' => ['sometimes', 'integer', 'in:0,1,2,3'],
            'order_date' => ['sometimes', 'date_format:Y-m-d'],
        ];
    }
}
