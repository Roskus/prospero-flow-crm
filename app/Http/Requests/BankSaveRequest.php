<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BankSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'uuid' => 'nullable|string|uuid',
            'name' => 'required|string|max:255',
            'country_id' => 'required|string',
            'bic' => $this->filled('uuid')
                ? 'required|bic'
                : 'required|bic|unique:bank,bic',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'phone' => 'nullable|string',
        ];
    }
}
