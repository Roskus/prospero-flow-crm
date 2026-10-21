<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Customer;

use App\Models\Customer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CustomerUpdateControllerTest extends TestCase
{
    #[Test]
    public function it_renders_searchable_selects_with_customer_values_selected(): void
    {
        $customer = Customer::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $this->user->id,
            'country_id' => 'es',
        ]);

        $response = $this->get('/customer/update/'.$customer->id);

        $response->assertOk();
        $content = $response->getContent();
        $this->assertMatchesRegularExpression('/<select name="country_id"[^>]*data-tom-select/', $content);
        $this->assertMatchesRegularExpression('/<select name="industry_id"[^>]*data-tom-select/', $content);
        $this->assertMatchesRegularExpression('/<select name="seller_id"[^>]*data-tom-select/', $content);
        $this->assertMatchesRegularExpression('/<option value="es"\\s+selected="selected"\\s*>/', $content);
        $this->assertMatchesRegularExpression('/<option value="'.$this->user->id.'"\\s+selected="selected"\\s*>/', $content);
        $response->assertSee('tom-select@2.4.3', false);
    }

    #[Test]
    public function it_renders_translated_address_search_with_feedback(): void
    {
        $this->user->update(['lang' => 'es']);
        $customer = Customer::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $this->user->id,
        ]);

        $response = $this->get('/customer/update/'.$customer->id);

        $response->assertOk();
        $response->assertSee('Buscar una dirección');
        $response->assertSee('id="search-address-feedback"', false);
        $response->assertSee(json_encode('No se encontró la dirección'), false);
    }

    #[Test]
    public function it_loads_the_contact_script_once(): void
    {
        $customer = Customer::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $this->user->id,
        ]);

        $response = $this->get('/customer/update/'.$customer->id);

        $response->assertOk();
        $this->assertSame(1, substr_count($response->getContent(), '/asset/js/Contact.js'));
    }

    #[Test]
    public function it_renders_translated_address_extra_and_selected_industry(): void
    {
        $this->user->update(['lang' => 'es']);
        $customer = Customer::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $this->user->id,
        ]);

        $response = $this->get('/customer/update/'.$customer->id);

        $response->assertOk();
        $response->assertSee('Dirección adicional (ej: Piso / Depto)');
        $response->assertDontSee('Address extra');
        $this->assertMatchesRegularExpression('/<option value="'.$customer->industry_id.'"\\s+selected="selected"\\s*>/', $response->getContent());
    }
}
