<?php

declare(strict_types=1);

namespace Tests\Unit\ValueObjects;

use App\ValueObjects\Address;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AddressTest extends TestCase
{
    #[Test]
    public function it_formats_a_full_address_in_one_line(): void
    {
        $address = Address::fromArray([
            'country_id' => 'AR',
            'province' => 'Santa Fe',
            'city' => 'Rosario',
            'street' => 'Córdoba 1234',
            'address_extra' => 'Piso 3 B',
            'zipcode' => '2000',
        ]);

        $this->assertSame('Córdoba 1234, Piso 3 B, 2000 Rosario, Santa Fe, Argentina', $address->oneLine());
        $this->assertSame(['Córdoba 1234, Piso 3 B', '2000 Rosario', 'Santa Fe', 'Argentina'], $address->lines());
    }

    #[Test]
    public function it_skips_empty_parts(): void
    {
        $address = Address::fromArray(['country_id' => 'es', 'city' => 'Madrid']);

        $this->assertSame('Madrid, Spain', $address->oneLine());
        $this->assertFalse($address->isEmpty());
    }

    #[Test]
    public function it_includes_the_locality(): void
    {
        $address = Address::fromArray(['street' => 'Thames 100', 'locality' => 'Palermo', 'city' => 'Buenos Aires']);

        $this->assertSame('Thames 100, Palermo, Buenos Aires', $address->oneLine());
    }

    #[Test]
    public function it_is_empty_without_values(): void
    {
        $address = Address::fromArray(['street' => '  ', 'city' => '', 'latitude' => '']);

        $this->assertTrue($address->isEmpty());
        $this->assertSame('', $address->oneLine());
        $this->assertSame([], $address->lines());
    }

    #[Test]
    public function it_turns_empty_strings_into_null_and_lowercases_the_country(): void
    {
        $address = Address::fromArray(['country_id' => 'ES', 'province' => '', 'street' => ' Gran Vía 1 ']);

        $this->assertSame('es', $address->countryId);
        $this->assertNull($address->province);
        $this->assertSame('Gran Vía 1', $address->street);
        $this->assertNull($address->locality);
    }

    #[Test]
    public function it_converts_back_to_column_names(): void
    {
        $data = [
            'country_id' => 'es',
            'province' => 'Madrid',
            'city' => 'Madrid',
            'locality' => 'Chamberí',
            'street' => 'Calle Mayor 1',
            'address_extra' => '2º A',
            'zipcode' => '28001',
            'latitude' => 40.4167,
            'longitude' => -3.70325,
        ];

        $this->assertSame($data, Address::fromArray($data)->toArray());
    }

    #[Test]
    public function it_needs_both_coordinates(): void
    {
        $this->assertTrue(Address::fromArray(['latitude' => '40.4167', 'longitude' => '-3.70325'])->hasCoordinates());
        $this->assertFalse(Address::fromArray(['latitude' => '40.4167'])->hasCoordinates());
    }
}
