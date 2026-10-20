<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CompanyIndexRequest extends FormRequest
{
    /**
     * Company administrators see their own companies; only SuperAdmin sees every company.
     */
    public function authorize(): bool
    {
        return Auth::user()?->hasRole('SuperAdmin') || Auth::user()?->isCompanyAdmin();
    }

    public function rules(): array
    {
        return [
            'filter' => Auth::user()->hasRole('SuperAdmin') ? 'nullable|in:active,deleted,all' : 'prohibited',
        ];
    }
}
