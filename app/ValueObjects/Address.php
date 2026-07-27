<?php

declare(strict_types=1);

namespace App\ValueObjects;

readonly class Address
{
    public function __construct(
        public ?string $street = null,
        public ?string $city = null,
        public ?string $province = null,
        public ?string $zipcode = null,
        public ?string $country_id = null,
        public ?string $locality = null,
        public ?string $address_extra = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            street: $data['street'] ?? null,
            city: $data['city'] ?? null,
            province: $data['province'] ?? null,
            zipcode: $data['zipcode'] ?? null,
            country_id: $data['country_id'] ?? null,
            locality: $data['locality'] ?? null,
            address_extra: $data['address_extra'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'street' => $this->street,
            'city' => $this->city,
            'province' => $this->province,
            'zipcode' => $this->zipcode,
            'country_id' => $this->country_id,
            'locality' => $this->locality,
            'address_extra' => $this->address_extra,
        ];
    }

    public function isEmpty(): bool
    {
        return $this->street === null
            && $this->city === null
            && $this->province === null
            && $this->zipcode === null
            && $this->country_id === null;
    }

    public function __toString(): string
    {
        return implode(', ', array_filter([
            $this->street,
            $this->city,
            $this->province,
            $this->zipcode,
        ]));
    }
}
