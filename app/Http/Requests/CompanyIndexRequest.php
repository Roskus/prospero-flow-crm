<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CompanyIndexRequest extends FormRequest
{
    /**
     * Company administrators see their own companies; only SuperAdmin sees every company.
     */
    public function authorize(): bool
    {
        return Auth::user()?->hasAnyRole(['SuperAdmin', 'CompanyAdmin', User::MULTI_COMPANY_ADMIN]) ?? false;
    }

    public function rules(): array
    {
        return [
            'filter' => Auth::user()->hasRole('SuperAdmin') ? 'nullable|in:active,deleted,all' : 'prohibited',
        ];
    }
}
