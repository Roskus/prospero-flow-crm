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
        $response->assertSee(__('Name'));
        $response->assertSee(__('Phone'));
        $response->assertSee(__('Country'));
        $response->assertSee(__('Street'));
        $response->assertSee(__('Facebook'));
        $response->assertSee(__('Status'));
        $response->assertSee(__('Seller'));
    }
}
