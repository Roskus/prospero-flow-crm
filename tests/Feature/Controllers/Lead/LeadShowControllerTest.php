<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Lead;

use App\Models\Lead;
use Tests\TestCase;

class LeadShowControllerTest extends TestCase
{
    #[Test]
    public function it_can_show_lead(): void
    {
        $lead = Lead::factory()->create();

        $response = $this->get('/lead/show/'.$lead->id);

        $response->assertOk();
        $response->assertViewIs('lead_customer.show');
        $response->assertSee($lead->name);
        $response->assertSee($lead->business_name);
        $response->assertSee($lead->phone);
    }
}
