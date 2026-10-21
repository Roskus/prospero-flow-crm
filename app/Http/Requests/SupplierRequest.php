<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\AddressRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class SupplierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('country_id'))) {
            $this->merge(['country_id' => strtolower(trim($this->input('country_id')))]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:50'],
            'email' => ['nullable', 'email', 'max:254'],
            'phone' => ['nullable', 'max:15'],
            ...AddressRules::rules(true),
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @return void
     *
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        // Verificar si la solicitud proviene de una API
        if ($this->expectsJson()) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'data' => $validator->errors(),
            ], 422));
        }

        parent::failedValidation($validator);
    }
}
