<?php

declare(strict_types=1);

namespace App\Http\Requests\Company;

use App\Models\Company;
use App\Rules\ValidateSafeSvg;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CompanySaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        // SuperAdmin manages every company and is the only one who can create companies
        // With ID = update own company (need update company permission + company_id match)
        if (Auth::user()->hasRole('SuperAdmin')) {
            return true;
        }

        if (empty($this->companyId())) {
            return false;
        }

        return Auth::user()->can('update company') && (int) $this->companyId() === (int) Auth::user()->company_id;
    }

    /**
     * The route id (API) takes precedence over the body id (web form) so both can't point to different companies.
     */
    public function companyId(): mixed
    {
        return $this->route('id') ?? $this->input('id');
    }

    public function rules(): array
    {
        $rules = [
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
            'currency' => 'required|string|max:3',
            'website' => 'nullable|url',
            'last_order_number' => 'nullable|integer|min:0',
            'inactivity_lock_time' => 'nullable|integer|min:1',
            'vacation_days_per_year' => 'nullable|integer|min:0',
            'personal_days_per_year' => 'nullable|integer|min:0',
            'weekly_hours_full_time' => 'nullable|numeric|min:0',
            'dashboard_calendar_view' => 'nullable|in:'.Company::CALENDAR_VIEW_MONTH.','.Company::CALENDAR_VIEW_WEEK,
            // FIX for CWE-434 (Unrestricted Upload) + CWE-79 (Stored XSS)
            // - Explicit MIME type allow-list (jpg, jpeg, png, webp, svg)
            // - Maximum file size of 2MB
            // - Custom validation to prevent embedded scripts in SVG
            'logo' => ['nullable', 'mimes:jpeg,jpg,png,webp,svg', 'max:2048', new ValidateSafeSvg],
        ];

        // Only SuperAdmin can change the status; a tampered request from any other user is rejected
        $rules['status'] = Auth::user()->hasRole('SuperAdmin')
            ? 'nullable|integer|in:'.Company::INACTIVE.','.Company::ACTIVE
            : 'prohibited';

        $rules['managed_companies_limit'] = Auth::user()->hasRole('SuperAdmin')
            ? 'nullable|integer|min:0|max:65535'
            : 'prohibited';
        $rules['parent_company_id'] = Auth::user()->hasRole('SuperAdmin')
            ? [
                'nullable',
                'integer',
                'different:id',
                Rule::exists('company', 'id')->whereNull('parent_company_id')->whereNull('deleted_at'),
                Rule::prohibitedIf(fn (): bool => ! empty($this->companyId()) && Company::query()->where('parent_company_id', $this->companyId())->exists()),
            ]
            : 'prohibited';

        return $rules;
    }

    public function messages(): array
    {
        return [
            'logo.image' => __('The logo must be a valid image file.'),
            'logo.mimes' => __('The logo must be a JPG, PNG, or WebP file.'),
            'logo.max' => __('The logo file size must not exceed 2 MB.'),
            'status.prohibited' => __('You are not allowed to change the company status.'),
            'managed_companies_limit.prohibited' => __('You are not allowed to change the client companies quota.'),
            'parent_company_id.prohibited' => __('You are not allowed to change the parent company.'),
            'parent_company_id.prohibited_if' => __('A company with client companies cannot be a client company.'),
            'parent_company_id.exists' => __('The parent company cannot be a client company.'),
        ];
    }
}
