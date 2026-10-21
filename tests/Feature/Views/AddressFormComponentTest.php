<?php

declare(strict_types=1);

namespace Tests\Feature\Views;

use App\ValueObjects\Address;
use Illuminate\Support\Facades\App;
use PHPUnit\Framework\Attributes\Test;
use Squire\Models\Country;
use Tests\TestCase;

class AddressFormComponentTest extends TestCase
{
    #[Test]
    public function it_renders_every_field_with_the_address_values(): void
    {
        $view = $this->view('components.address.form', [
            'address' => Address::fromArray([
                'country_id' => 'es',
                'province' => 'Madrid',
                'city' => 'Madrid',
                'locality' => 'Chamberí',
                'street' => 'Calle Mayor 1',
                'address_extra' => '2º A',
                'zipcode' => '28001',
                'latitude' => '40.4167',
                'longitude' => '-3.70325',
            ]),
            'countries' => Country::orderBy('name')->get(),
            'required' => true,
        ]);

        $view->assertSee('id="search-address"', false);
        $view->assertSee('id="map"', false);
        $this->assertMatchesRegularExpression('/<option value="es"\s+selected="selected"\s*>/', (string) $view);
        foreach (['Madrid', 'Chamberí', 'Calle Mayor 1', '2º A', '28001', '40.4167', '-3.70325'] as $value) {
            $view->assertSee('value="'.$value.'"', false);
        }
    }

    #[Test]
    public function it_shows_spanish_labels_for_the_extra_address(): void
    {
        App::setLocale('es');

        $view = $this->view('components.address.form', [
            'address' => new Address,
            'countries' => Country::orderBy('name')->get(),
        ]);

        $view->assertSee('Dirección adicional');
        $view->assertSee('placeholder="Piso, oficina, depto..."', false);
        $view->assertDontSee('Floor, office, suite...');
    }

    #[Test]
    public function the_display_component_renders_nothing_for_an_empty_address(): void
    {
        $view = $this->view('components.address.display', ['address' => new Address]);

        $this->assertSame('', trim((string) $view));
    }

    #[Test]
    public function the_display_component_renders_one_line_per_part(): void
    {
        $view = $this->view('components.address.display', [
            'address' => Address::fromArray(['street' => 'Calle Mayor 1', 'zipcode' => '28001', 'city' => 'Madrid']),
            'format' => 'lines',
        ]);

        $view->assertSee('<div>Calle Mayor 1</div>', false);
        $view->assertSee('<div>28001 Madrid</div>', false);
    }
}
