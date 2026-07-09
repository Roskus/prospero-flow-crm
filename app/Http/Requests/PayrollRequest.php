<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayrollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:user,id',
            'gross_amount' => 'required|numeric|min:0',
            'net_amount' => 'required|numeric|min:0',
            'period_year' => 'required|integer|min:2000|max:2099',
            'period_month' => 'required|integer|between:1,12',
            'payment_date' => 'nullable|date',
            'iban' => 'nullable|string|max:34',
            'notes' => 'nullable|string|max:500',
            'file' => 'nullable|file|mimes:pdf|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => __('The employee is required.'),
            'gross_amount.required' => __('The gross amount is required.'),
            'net_amount.required' => __('The net amount is required.'),
            'period_year.required' => __('The period year is required.'),
            'period_month.required' => __('The period month is required.'),
        ];
    }
}
