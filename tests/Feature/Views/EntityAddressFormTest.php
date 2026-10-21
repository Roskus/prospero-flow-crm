<?php

declare(strict_types=1);

namespace Tests\Feature\Views;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EntityAddressFormTest extends TestCase
{
    private const array ADDRESS = [
        'country_id' => 'es',
        'province' => 'Madrid',
        'city' => 'Alcalá',
        'locality' => 'Chamberí',
        'street' => 'Calle Mayor 1',
        'address_extra' => '2º A',
        'zipcode' => '28001',
    ];

    /**
     * @return array<string, array{class-string<Model>, string}>
     */
    public static function entities(): array
    {
        return [
            'lead' => [Lead::class, '/lead/update/'],
            'customer' => [Customer::class, '/customer/update/'],
            'company' => [Company::class, '/company/update/'],
            'supplier' => [Supplier::class, '/supplier/update/'],
        ];
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    #[Test]
    #[DataProvider('entities')]
    public function the_form_shows_every_address_field_with_its_values(string $modelClass, string $url): void
    {
        $attributes = $modelClass === Company::class
            ? self::ADDRESS
            : [...self::ADDRESS, 'company_id' => $this->user->company_id];
        if (in_array($modelClass, [Lead::class, Customer::class], true)) {
            $attributes['seller_id'] = $this->user->id;
        }
        $model = $modelClass::factory()->create($attributes);

        $response = $this->get($url.$model->id);

        $response->assertOk();
        $this->assertAddressForm($response);
    }

    private function assertAddressForm(TestResponse $response): void
    {
        foreach (['country_id', 'province', 'city', 'locality', 'street', 'address_extra', 'zipcode', 'latitude', 'longitude'] as $field) {
            $response->assertSee('name="'.$field.'"', false);
        }
        $response->assertSee('id="search-address"', false);
        $response->assertSee('id="map"', false);
        $this->assertMatchesRegularExpression('/<option value="es"\s+selected="selected"\s*>/', $response->getContent());
        foreach (['Alcalá', 'Chamberí', 'Calle Mayor 1', '2º A', '28001'] as $value) {
            $response->assertSee('value="'.$value.'"', false);
        }
    }
}
