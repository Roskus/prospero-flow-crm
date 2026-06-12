<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Squire\Models\Currency;

class CurrencySettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $conversionRates = collect((array) $this->input('conversion_rates'))
            ->mapWithKeys(function (mixed $rate, string|int $currency): array {
                return [
                    strtoupper((string) $currency) => $rate === '' ? null : $rate,
                ];
            })
            ->all();

        $this->merge([
            'conversion_rates' => $conversionRates,
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'conversion_rates' => ['required', 'array'],
            'conversion_rates.*' => ['nullable', 'numeric', 'gt:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            foreach (array_keys((array) $this->input('conversion_rates', [])) as $currency) {
                if (Currency::find(strtolower((string) $currency)) === null) {
                    $validator->errors()->add(
                        "conversion_rates.$currency",
                        __('The selected :attribute is invalid.', ['attribute' => __('Currency')])
                    );
                }
            }
        });
    }
}
