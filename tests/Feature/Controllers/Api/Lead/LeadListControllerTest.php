<?php

namespace Tests\Feature\Controllers\Api\Lead;

use App\Models\Lead;
use App\Models\User;
use Tests\TestCase;

class LeadListControllerTest extends TestCase
{
    /** @test */
    public function it_can_list_lead(): void
    {
        $this->actingAs(User::factory()->create(), 'api');
        $leads = Lead::factory()->count(2)->create(['company_id' => auth()->user()->company_id, 'seller_id' => auth()->id()]);

        $response = $this->get('/api/lead');

        $this->assertEquals($response->json()['count'], $leads->count());
        $this->assertEquals(array_except($response->json()['leads'][0], ['seller', 'industry', 'country']), $leads[0]->toArray());
        $this->assertEquals(array_except($response->json()['leads'][1], ['seller', 'industry', 'country']), $leads[1]->toArray());
    }
}
