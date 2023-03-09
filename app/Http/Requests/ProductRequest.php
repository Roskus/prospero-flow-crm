<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'id' => 'nullable',
            'category_id' => 'required',
            'brand_id' => 'required',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|numeric|min:0',
            'sku' => 'nullable',
            'min_stock_quantity' => 'required|numeric|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:3072', // 3 MB max
            'barcode' => 'nullable|numeric|digits_between:1,48',
            'elaboration_date' => ['nullable', 'date', 'before_or_equal:today'],
            'expiration_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:today'],
        ];
    }
}
