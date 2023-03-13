<?php

namespace Tests\Feature\Controllers\Lead;

use Tests\TestCase;

class LeadCreateControllerTest extends TestCase
{
    /** @test */
    public function it_can_create_lead()
    {
        $response = $this->get('/lead/create');

        $response->assertOk();
        $response->assertViewIs('lead.lead');
        $response->assertSee('Name');
        $response->assertSee('Phone');
        $response->assertSee('Country');
        $response->assertSee('Street');
        $response->assertSee('Facebook');
        $response->assertSee('Status');
        $response->assertSee('Seller');
    }
}
