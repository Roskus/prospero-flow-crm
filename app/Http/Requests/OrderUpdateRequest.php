<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Helpers\CurrencyHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OrderUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('update order') ?? false;
    }

    public function rules(): array
    {
        return [
            'amount' => ['sometimes', 'numeric', 'min:0.01', 'max:'.CurrencyHelper::MAX_AMOUNT],
            'branch_id' => ['sometimes', 'integer', Rule::exists('branch', 'id')->where('company_id', Auth::user()->company_id)->whereNull('deleted_at')],
            'currency' => ['sometimes', 'string', 'max:3'],
            'status' => ['sometimes', 'integer', 'in:0,1,2,3'],
            'order_date' => ['sometimes', 'date_format:Y-m-d'],
        ];
    }
}
