<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReportSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('read report') ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'seller_id' => ['nullable', 'integer', Rule::exists('user', 'id')->where('company_id', Auth::user()->company_id)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'from.date' => __('The start date is not valid.'),
            'to.date' => __('The end date is not valid.'),
            'to.after_or_equal' => __('The end date must be on or after the start date.'),
            'seller_id.exists' => __('The selected seller is not valid.'),
        ];
    }
}
