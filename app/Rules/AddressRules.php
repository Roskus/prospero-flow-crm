<?php

declare(strict_types=1);

namespace App\Rules;

use Squire\Rules\CountryRule;

/**
 * Validation rules of the address fields, matching the address column lengths.
 */
class AddressRules
{
    /**
     * @return array<string, list<mixed>>
     */
    public static function rules(bool $countryRequired = false): array
    {
        return [
            'country_id' => [$countryRequired ? 'required' : 'nullable', 'string', 'max:2', new CountryRule('code_2')],
            'province' => ['nullable', 'string', 'max:80'],
            'city' => ['nullable', 'string', 'max:50'],
            'locality' => ['nullable', 'string', 'max:80'],
            'street' => ['nullable', 'string', 'max:80'],
            'address_extra' => ['nullable', 'string', 'max:80'],
            'zipcode' => ['nullable', 'string', 'max:10'],
            'latitude' => ['nullable', 'numeric', 'gte:-90', 'lte:90', 'required_with:longitude'],
            'longitude' => ['nullable', 'numeric', 'gte:-180', 'lte:180', 'required_with:latitude'],
        ];
    }
}
