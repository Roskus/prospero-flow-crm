<?php

namespace Tests\Feature\Controllers\Lead;

use App\Models\Lead;
use Tests\TestCase;

class LeadIndexControllerTest extends TestCase
{
    /** @test */
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
}
