<?php

namespace Tests\Feature\Controllers\Lead;

use App\Models\Lead;
use Tests\TestCase;

class LeadShowControllerTest extends TestCase
{
    /** @test */
    public function it_can_show_lead()
    {
        $lead = Lead::factory()->create();

        $response = $this->get('/lead/show/'.$lead->id);

        $response->assertOk();
        $response->assertViewIs('lead_customer.show');
        $response->assertSee($lead->name);
        $response->assertSee($lead->business_name);
        $response->assertSee($lead->phone);
        $response->assertSee('Contacts');
    }
}
