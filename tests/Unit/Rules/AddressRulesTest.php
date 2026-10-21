<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Rules\AddressRules;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AddressRulesTest extends TestCase
{
    #[Test]
    public function it_defines_every_address_field(): void
    {
        $this->assertSame(
            ['country_id', 'province', 'city', 'locality', 'street', 'address_extra', 'zipcode', 'latitude', 'longitude'],
            array_keys(AddressRules::rules())
        );
    }

    #[Test]
    public function a_valid_address_passes(): void
    {
        $validator = Validator::make([
            'country_id' => 'ar',
            'province' => 'Santa Fe',
            'city' => 'Rosario',
            'street' => 'Córdoba 1234',
            'address_extra' => 'Piso 3 B',
            'zipcode' => '2000',
            'latitude' => '-32.9468',
            'longitude' => '-60.6393',
        ], AddressRules::rules(true));

        $this->assertTrue($validator->passes());
    }

    #[Test]
    public function the_country_is_only_required_when_asked(): void
    {
        $this->assertTrue(Validator::make([], AddressRules::rules())->passes());
        $this->assertArrayHasKey('country_id', Validator::make([], AddressRules::rules(true))->errors()->toArray());
    }

    #[Test]
    public function an_unknown_country_fails(): void
    {
        $validator = Validator::make(['country_id' => 'zz'], AddressRules::rules());

        $this->assertArrayHasKey('country_id', $validator->errors()->toArray());
    }

    #[Test]
    public function a_street_over_80_characters_fails(): void
    {
        $validator = Validator::make(['street' => Str::repeat('a', 81)], AddressRules::rules());

        $this->assertSame(['street'], array_keys($validator->errors()->toArray()));
    }

    #[Test]
    public function a_lone_latitude_fails_on_the_longitude(): void
    {
        $validator = Validator::make(['latitude' => '40.4167'], AddressRules::rules());

        $this->assertSame(['longitude'], array_keys($validator->errors()->toArray()));
    }
}
