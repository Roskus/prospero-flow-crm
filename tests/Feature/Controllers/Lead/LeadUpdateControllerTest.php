<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Lead;

use App\Models\Lead;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LeadUpdateControllerTest extends TestCase
{
    #[Test]
    public function it_renders_country_select_with_lead_country_selected(): void
    {
        $lead = Lead::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $this->user->id,
            'country_id' => 'es',
        ]);

        $response = $this->get('/lead/update/'.$lead->id);

        $response->assertOk();
        $response->assertSee('<select name="country_id"', false);
        $this->assertMatchesRegularExpression('/<option value="es"\s+selected="selected"\s*>/', $response->getContent());
        $this->assertDoesNotMatchRegularExpression('/<option value="fr"\s+selected="selected"\s*>/', $response->getContent());
        $this->assertMatchesRegularExpression('/<select name="industry_id"[^>]*data-tom-select/', $response->getContent());
        $this->assertMatchesRegularExpression('/<select name="seller_id"[^>]*data-tom-select/', $response->getContent());
        $response->assertSee('tom-select@2.4.3', false);
    }

    #[Test]
    public function it_renders_translated_address_search_with_feedback(): void
    {
        $this->user->update(['lang' => 'es']);
        $lead = Lead::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $this->user->id,
        ]);

        $response = $this->get('/lead/update/'.$lead->id);

        $response->assertOk();
        $response->assertSee('Buscar una dirección');
        $response->assertSee('id="search-address-feedback"', false);
        $response->assertSee(json_encode('No se encontró la dirección'), false);
    }

    #[Test]
    public function it_loads_the_contact_script_once(): void
    {
        $lead = Lead::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $this->user->id,
        ]);

        $response = $this->get('/lead/update/'.$lead->id);

        $response->assertOk();
        $this->assertSame(1, substr_count($response->getContent(), '/asset/js/Contact.js'));
    }

    #[Test]
    public function it_renders_translated_address_extra_and_selected_industry(): void
    {
        $this->user->update(['lang' => 'es']);
        $lead = Lead::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $this->user->id,
        ]);

        $response = $this->get('/lead/update/'.$lead->id);

        $response->assertOk();
        $response->assertSee('Dirección adicional (ej: Piso / Depto)');
        $response->assertDontSee('Address extra');
        $this->assertMatchesRegularExpression('/<option value="'.$lead->industry_id.'"\\s+selected="selected"\\s*>/', $response->getContent());
    }
}
