<?php

namespace Tests\Feature\Controllers\Api\Lead;

use App\Models\Lead;
use App\Models\User;
use Tests\TestCase;

class LeadCreateControllerTest extends TestCase
{
    /** @test */
    public function it_can_save_lead(): void
    {
        $this->actingAs(User::factory()->create(), 'api');

        $data = array_except(Lead::factory()->create(['seller_id' => auth()->id()])->toArray(), 'id');
        $data['tags'] = implode(',', $data['tags']);

        $response = $this->post('/api/lead', $data);

        $response->assertJsonFragment([
            'lead' => [
                'id' => Lead::all()->last()->id,
            ],
        ]);

        $this->equalTo(Lead::all()->last(), $data);
    }
}
