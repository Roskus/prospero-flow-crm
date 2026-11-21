<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\MaintenanceType;
use App\Http\Requests\Concerns\ValidatesCurrency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AssetMaintenanceSaveRequest extends FormRequest
{
    use ValidatesCurrency;

    public function authorize(): bool
    {
        return Auth::user()?->can('update asset') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'id' => ['nullable', 'integer'],
            'asset_id' => ['required', 'integer'],
            'type' => ['required', Rule::enum(MaintenanceType::class)],
            'date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:2000'],
            'cost' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
            'currency' => $this->currencyRules(),
            'supplier_id' => ['nullable', 'integer', Rule::exists('supplier', 'id')->where('company_id', Auth::user()->company_id)->whereNull('deleted_at')],
            'odometer_km' => ['nullable', 'integer', 'min:0', 'max:4294967295'],
            'usage_hours' => ['nullable', 'integer', 'min:0', 'max:4294967295'],
            'next_due_at' => ['nullable', 'date', 'after_or_equal:date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'type.required' => __('The maintenance type is required.'),
            'date.required' => __('The maintenance date is required.'),
            'next_due_at.after_or_equal' => __('The next due date cannot be earlier than the maintenance date.'),
            'supplier_id.exists' => __('The selected supplier is not valid.'),
        ];
    }
}
