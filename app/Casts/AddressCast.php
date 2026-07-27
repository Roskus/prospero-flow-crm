<?php

declare(strict_types=1);

namespace App\Casts;

use App\ValueObjects\Address;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class AddressCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): Address
    {
        return new Address(
            street: $attributes['street'] ?? null,
            city: $attributes['city'] ?? null,
            province: $attributes['province'] ?? null,
            zipcode: $attributes['zipcode'] ?? null,
            country_id: $attributes['country_id'] ?? null,
            locality: $attributes['locality'] ?? null,
            address_extra: $attributes['address_extra'] ?? null,
        );
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        if ($value === null) {
            return [
                'street' => null,
                'city' => null,
                'province' => null,
                'zipcode' => null,
                'country_id' => null,
                'locality' => null,
                'address_extra' => null,
            ];
        }

        return [
            'street' => $value->street,
            'city' => $value->city,
            'province' => $value->province,
            'zipcode' => $value->zipcode,
            'country_id' => $value->country_id,
            'locality' => $value->locality,
            'address_extra' => $value->address_extra,
        ];
    }
}
