<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Lead;

use App\Models\Lead;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LeadUpdateControllerTest extends TestCase
{
    #[Test]
    public function it_can_update_lead(): void
    {
        $this->actingAs(User::factory()->create(), 'api');

        $lead = Lead::factory()->create(['seller_id' => auth()->id()])->toArray();
        $lead['tags'] = implode(',', $lead['tags']);

        $updatedLead = array_except(Lead::factory()->create(['seller_id' => auth()->id()])->toArray(), 'id');
        $updatedLead['tags'] = implode(',', $updatedLead['tags']);
        $updatedLead['id'] = $lead['id'];
        $updatedLead['vat'] = strtoupper($updatedLead['vat']);

        $response = $this->post('/api/lead', $updatedLead);

        $lead['tags'] = explode(',', $lead['tags']);
        $updatedLead['tags'] = explode(',', $updatedLead['tags']);

        $response->assertJsonFragment([
            'lead' => [
                'id' => $lead['id'],
            ],
        ]);

        $this->assertEquals(array_except(Lead::find($lead['id'])->toArray(), ['seller', 'industry', 'country', 'company']), $updatedLead);
        $this->assertNotEquals(array_except(Lead::find($lead['id'])->toArray(), ['seller', 'industry', 'country', 'company']), $lead);
    }
}
