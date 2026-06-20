<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BankDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (int) Auth::user()->company_id === Company::DEFAULT_COMPANY;
    }

    public function rules(): array
    {
        return [];
    }
}
