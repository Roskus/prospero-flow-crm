<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Lead;

use App\Models\Lead;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LeadIndexControllerTest extends TestCase
{
    #[Test]
    public function it_can_view_index_leads()
    {
        $lead = Lead::factory()->create(['company_id' => $this->user->company_id, 'status' => Lead::OPEN]);
        $lead2 = Lead::factory()->create(['company_id' => $this->user->company_id, 'status' => Lead::CLOSED]);

        $response = $this->get('/lead');
        $response->assertOk();
        $response->assertSee($lead->name);
        $response->assertSee($lead2->name);

        // SEARCH
        $response = $this->get('/lead?search='.$lead->name);
        $response->assertSee($lead->name);
        $response->assertDontSee($lead2->name);

        // FILTERS
        $response = $this->get('/lead?country_id='.$lead->country_id);
        $response->assertSee($lead->name);
        $response->assertDontSee($lead2->name);

        $response = $this->get('/lead?seller_id='.$lead->seller_id);
        $response->assertSee($lead->name);
        $response->assertDontSee($lead2->name);

        $response = $this->get('/lead?status='.$lead->status);
        $response->assertSee($lead->name);
        $response->assertDontSee($lead2->name);

        $response = $this->get('/lead?industry_id='.$lead->industry->id);
        $response->assertSee($lead->name);
        $response->assertDontSee($lead2->name);
    }

    #[Test]
    public function it_filters_leads_by_country_with_searchable_select(): void
    {
        $spanishLead = Lead::factory()->create(['company_id' => $this->user->company_id, 'country_id' => 'es']);
        $frenchLead = Lead::factory()->create(['company_id' => $this->user->company_id, 'country_id' => 'fr']);

        $response = $this->get('/lead?country_id=es');

        $response->assertOk();
        $response->assertSee($spanishLead->name);
        $response->assertDontSee($frenchLead->name);
        $this->assertMatchesRegularExpression('/<select name="country_id"[^>]*data-tom-select/', $response->getContent());
        $this->assertMatchesRegularExpression('/<option value="es"\s+selected="selected"\s*>/', $response->getContent());
        $response->assertSee('tom-select@2.4.3', false);
        $response->assertSee('clear_button', false);
    }

    #[Test]
    public function it_filters_leads_by_industry_with_searchable_select(): void
    {
        $lead = Lead::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->get('/lead?industry_id='.$lead->industry_id);

        $response->assertOk();
        $response->assertSee($lead->name);
        $this->assertMatchesRegularExpression('/<select name="industry_id"[^>]*data-tom-select/', $response->getContent());
        $this->assertMatchesRegularExpression('/<option value="'.$lead->industry_id.'"\\s+selected="selected"\\s*>/', $response->getContent());
    }
}
