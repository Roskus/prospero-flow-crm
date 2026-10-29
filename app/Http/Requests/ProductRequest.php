<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\DimensionUnit;
use App\Enums\VolumeUnit;
use App\Enums\WeightUnit;
use App\Helpers\CurrencyHelper;
use App\Http\Requests\Concerns\ValidatesCurrency;
use App\Rules\ExtensionMatchesContent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    use ValidatesCurrency;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()?->can('create product') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id' => 'nullable',
            'category_id' => 'required',
            'brand_id' => 'required',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0|max:'.CurrencyHelper::MAX_AMOUNT,
            'price' => 'required|numeric|min:0|max:'.CurrencyHelper::MAX_AMOUNT,
            'currency' => $this->currencyRules(),
            'tax' => 'nullable|numeric|min:0',
            'quantity' => 'required|numeric|min:0',
            'sku' => 'nullable',
            'min_stock_quantity' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|decimal:0,3|min:0|max:9999999',
            'weight_unit' => ['nullable', Rule::enum(WeightUnit::class)],
            'volume' => 'nullable|numeric|decimal:0,3|min:0|max:9999999',
            'volume_unit' => ['nullable', Rule::enum(VolumeUnit::class)],
            'dimensions' => 'nullable|string|max:100',
            'dimension_unit' => ['nullable', Rule::enum(DimensionUnit::class)],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:3072', new ExtensionMatchesContent],
            'barcode' => 'nullable|numeric|digits_between:1,48',
            'elaboration_date' => ['nullable', 'date', 'before_or_equal:today'],
            'expiration_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:today'],
        ];
    }
}
