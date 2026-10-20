<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PayrollDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->hasRole('SuperAdmin') || Auth::user()?->isCompanyAdmin();
    }

    public function rules(): array
    {
        return [];
    }
}
