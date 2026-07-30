<?php

declare(strict_types=1);

namespace App\Http\Requests\Company;

use App\Rules\ValidateSafeSvg;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CompanySaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Empty ID = create (need create company permission)
        // With ID = update own company (need update company permission + company_id match)
        if (empty($this->id)) {
            return Auth::user()->can('create company');
        }

        return Auth::user()->can('update company') && (int) $this->id === (int) Auth::user()->company_id;
    }

    public function rules(): array
    {
        return [
            'id' => 'nullable|integer',
            'name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'vat' => 'nullable|string|max:30',
            'signature_html' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'country_id' => 'nullable|string|max:2',
            'province' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'zipcode' => 'nullable|string|max:20',
            'currency' => 'nullable|string|max:3',
            'website' => 'nullable|url',
            'last_order_number' => 'nullable|integer|min:0',
            'inactivity_lock_time' => 'nullable|integer|min:1',
            'vacation_days_per_year' => 'nullable|integer|min:0',
            'personal_days_per_year' => 'nullable|integer|min:0',
            'weekly_hours_full_time' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:active,inactive',
            // FIX for CWE-434 (Unrestricted Upload) + CWE-79 (Stored XSS)
            // - Explicit MIME type allow-list (jpg, jpeg, png, webp, svg)
            // - Maximum file size of 2MB
            // - Custom validation to prevent embedded scripts in SVG
            'logo' => ['nullable', 'mimes:jpeg,jpg,png,webp,svg', 'max:2048', new ValidateSafeSvg],
        ];
    }

    public function messages(): array
    {
        return [
            'logo.image' => __('The logo must be a valid image file.'),
            'logo.mimes' => __('The logo must be a JPG, PNG, or WebP file.'),
            'logo.max' => __('The logo file size must not exceed 2 MB.'),
        ];
    }
}
