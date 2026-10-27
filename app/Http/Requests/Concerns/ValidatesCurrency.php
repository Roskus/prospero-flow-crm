<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns;

use App\Helpers\CurrencyHelper;
use Illuminate\Validation\Rule;

trait ValidatesCurrency
{
    protected function prepareForValidation(): void
    {
        if ($this->filled('currency')) {
            $this->merge(['currency' => strtoupper((string) $this->input('currency'))]);
        }
    }

    /**
     * @return list<mixed>
     */
    protected function currencyRules(string $presence = 'nullable'): array
    {
        return [$presence, 'string', Rule::in(CurrencyHelper::codes())];
    }
}
