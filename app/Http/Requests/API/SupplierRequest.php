<?php

declare(strict_types=1);

namespace App\Http\Requests\API;

use App\Rules\AddressRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Squire\Rules\CountryRule;

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
     * The API receives the country as `country`, lowercased like the stored ISO codes.
     */
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('country'))) {
            $this->merge(['country' => strtolower(trim($this->input('country')))]);
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
            'email' => ['nullable', 'email:rfc,dns', 'max:254'],
            'phone' => ['nullable', 'max:15'],
            'country' => ['required', 'string', 'max:2', new CountryRule('code_2')],
            'business_name' => ['nullable', 'max:255'],
            'vat' => ['nullable', 'max:50'],
            'website' => ['nullable', 'url', 'max:255'],
            ...Arr::except(AddressRules::rules(), 'country_id'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
        ], 422));
    }
}
