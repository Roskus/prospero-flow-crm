<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Squire\Rules\CountryRule;

class CarrierSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('update asset') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(array_filter([
            'name' => is_string($this->input('name')) ? trim($this->input('name')) : null,
            'country_id' => is_string($this->input('country_id')) ? strtolower($this->input('country_id')) : null,
        ], fn (?string $value): bool => $value !== null));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'id' => ['nullable', 'integer'],
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('carrier', 'name')
                    ->where('company_id', Auth::user()->company_id)
                    ->where(fn (Builder $query) => $this->filled('country_id')
                        ? $query->where('country_id', $this->input('country_id'))
                        : $query->whereNull('country_id'))
                    ->whereNull('deleted_at')
                    ->ignore($this->integer('id') ?: null),
            ],
            'country_id' => ['nullable', 'string', 'max:2', new CountryRule('code_2')],
            'phone' => ['nullable', 'string', 'max:30'],
            'website' => ['nullable', 'url', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => __('The name is required.'),
            'name.unique' => __('A carrier with this name already exists in this country.'),
            'website.url' => __('The website must be a valid URL.'),
        ];
    }
}
