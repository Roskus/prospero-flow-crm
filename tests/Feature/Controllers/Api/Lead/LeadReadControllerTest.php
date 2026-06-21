<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Lead;

use App\Models\Lead;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LeadReadControllerTest extends TestCase
{
    #[Test]
    public function it_can_read_lead(): void
    {
        $this->actingAs($this->user, 'api');
        $lead = Lead::factory()->create(['company_id' => auth()->user()->company_id, 'seller_id' => auth()->id()]);

        $response = $this->get('/api/lead/'.$lead->id);

        $response->assertOk();
        $this->assertIsArray($response->json()['lead']);
        $this->assertEquals($response->json()['lead']['id'], $lead->id);
    }

    #[Test]
    public function it_not_found_a_lead(): void
    {
        $this->actingAs($this->user, 'api');

        $response = $this->get('/api/lead/9999');

        $response->assertNotFound();
        $response->assertExactJson(['lead' => null]);
    }
}
