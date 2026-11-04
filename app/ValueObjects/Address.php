<?php

declare(strict_types=1);

namespace App\ValueObjects;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use OpenApi\Attributes as OAT;
use Squire\Models\Country;

#[OAT\Schema(
    schema: 'Address',
    description: 'Postal address fields shared by every entity with an address',
    properties: [
        new OAT\Property(property: 'country_id', description: 'Country ISO 3166-1 alpha-2 code', type: 'string', example: 'es', maxLength: 2),
        new OAT\Property(property: 'province', description: 'Province or state', type: 'string', example: 'Madrid', maxLength: 80),
        new OAT\Property(property: 'city', description: 'City', type: 'string', example: 'Madrid', maxLength: 50),
        new OAT\Property(property: 'locality', description: 'Locality or neighbourhood', type: 'string', example: 'Chamberí', maxLength: 80),
        new OAT\Property(property: 'street', description: 'Street and number', type: 'string', example: 'Calle de Alcalá 42', maxLength: 80),
        new OAT\Property(property: 'address_extra', description: 'Floor, office or suite', type: 'string', example: 'Piso 3, Puerta B', maxLength: 80),
        new OAT\Property(property: 'zipcode', description: 'Postal code', type: 'string', example: '28014', maxLength: 10),
        new OAT\Property(property: 'latitude', description: 'Latitude', type: 'number', format: 'float', example: 40.4168, maximum: 90, minimum: -90),
        new OAT\Property(property: 'longitude', description: 'Longitude', type: 'number', format: 'float', example: -3.7038, maximum: 180, minimum: -180),
    ],
    type: 'object'
)]
/**
 * Postal address stored in the address columns of an entity (lead, customer, company, supplier...).
 *
 * @implements Arrayable<string, string|float|null>
 */
final readonly class Address implements Arrayable, Castable
{
    public const array COLUMNS = [
        'country_id',
        'province',
        'city',
        'locality',
        'street',
        'address_extra',
        'zipcode',
        'latitude',
        'longitude',
    ];

    public function __construct(
        public ?string $countryId = null,
        public ?string $province = null,
        public ?string $city = null,
        public ?string $locality = null,
        public ?string $street = null,
        public ?string $extra = null,
        public ?string $zipcode = null,
        public ?float $latitude = null,
        public ?float $longitude = null,
    ) {}

    /**
     * Build an address from request, import or column data, ignoring missing keys and empty values.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $countryId = self::clean($data['country_id'] ?? null);

        return new self(
            countryId: $countryId !== null ? strtolower($countryId) : null,
            province: self::clean($data['province'] ?? null),
            city: self::clean($data['city'] ?? null),
            locality: self::clean($data['locality'] ?? null),
            street: self::clean($data['street'] ?? null),
            extra: self::clean($data['address_extra'] ?? null),
            zipcode: self::clean($data['zipcode'] ?? null),
            latitude: self::coordinate($data['latitude'] ?? null),
            longitude: self::coordinate($data['longitude'] ?? null),
        );
    }

    /**
     * Copy of this address with the given fields replaced; fields missing from the data keep their value.
     *
     * @param  array<string, mixed>  $data
     */
    public function merge(array $data): self
    {
        return self::fromArray([...$this->toArray(), ...array_intersect_key($data, array_flip(self::COLUMNS))]);
    }

    /**
     * @return array{country_id: ?string, province: ?string, city: ?string, locality: ?string, street: ?string, address_extra: ?string, zipcode: ?string, latitude: ?float, longitude: ?float}
     */
    public function toArray(): array
    {
        return [
            'country_id' => $this->countryId,
            'province' => $this->province,
            'city' => $this->city,
            'locality' => $this->locality,
            'street' => $this->street,
            'address_extra' => $this->extra,
            'zipcode' => $this->zipcode,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }

    public function isEmpty(): bool
    {
        return $this->lines() === [] && ! $this->hasCoordinates();
    }

    public function hasCoordinates(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    public function countryName(): ?string
    {
        if ($this->countryId === null) {
            return null;
        }

        static $names = [];

        return $names[$this->countryId] ??= Country::find($this->countryId)?->getAttribute('name');
    }

    /**
     * Non empty parts in display order: street and extra, locality, zipcode and city, province, country.
     *
     * @return list<string>
     */
    public function lines(): array
    {
        return array_values(array_filter([
            self::join(', ', $this->street, $this->extra),
            $this->locality,
            self::join(' ', $this->zipcode, $this->city),
            $this->province,
            $this->countryName(),
        ]));
    }

    public function oneLine(): string
    {
        return implode(', ', $this->lines());
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    public static function castUsing(array $arguments): CastsAttributes
    {
        return new class implements CastsAttributes
        {
            /**
             * Columns may be changed directly, so the object is rebuilt on every read instead of synced back on save.
             */
            public bool $withoutObjectCaching = true;

            /**
             * @param  array<string, mixed>  $attributes
             */
            public function get(Model $model, string $key, mixed $value, array $attributes): Address
            {
                return Address::fromArray($attributes);
            }

            /**
             * @param  array<string, mixed>  $attributes
             * @return array<string, string|float|null>
             */
            public function set(Model $model, string $key, mixed $value, array $attributes): array
            {
                if (! $value instanceof Address) {
                    throw new InvalidArgumentException('The given value is not an Address instance.');
                }

                return $value->toArray();
            }
        };
    }

    private static function clean(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private static function coordinate(mixed $value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }

    private static function join(string $separator, ?string ...$parts): ?string
    {
        $parts = array_filter($parts);

        return $parts === [] ? null : implode($separator, $parts);
    }
}
