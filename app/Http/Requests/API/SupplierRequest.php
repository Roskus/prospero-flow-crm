<?php

declare(strict_types=1);

namespace App\Http\Requests\API;

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
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return $user->can('update supplier');
        }

        return $user->can('create supplier');
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
            'email' => ['nullable', 'email:rfc,dns', 'max:254'],
            'phone' => ['nullable', 'max:15'],
            'country' => ['required', 'max:2'],
            'business_name' => ['nullable', 'max:255'],
            'vat' => ['nullable', 'max:50'],
            'website' => ['nullable', 'url', 'max:255'],
            'province' => ['nullable', 'max:255'],
            'city' => ['nullable', 'max:255'],
            'street' => ['nullable', 'max:255'],
            'zipcode' => ['nullable', 'max:10'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
        ], 422));
    }
}
