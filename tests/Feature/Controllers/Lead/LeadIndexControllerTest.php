<?php

namespace Tests\Feature\Controllers\Lead;

use App\Models\Industry;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadIndexControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_view_index_leads()
    {
        $user = $this->signIn();
        $this->actingAs($user);

        $user2 = User::factory()->create();
        $industry = Industry::factory()->create();
        $industry2 = Industry::factory()->create();
        $lead = Lead::factory()->create(['industry_id' => $industry->id, 'country_id' => 1, 'seller_id' => $user->id, 'status' => 'open']);
        $lead2 = Lead::factory()->create(['industry_id' => $industry2->id, 'country_id' => 2, 'seller_id' => $user2->id, 'status' => 'recall']);

        $response = $this->get('/lead');
        $response->assertOk();
        $response->assertSee($lead->name);
        $response->assertSee($lead2->name);

        // SEARCH
        $response = $this->get('/lead?search='.$lead->name);
        $response->assertSee($lead->name);
        $response->assertDontSee($lead2->name);

        // FILTERS
        $response = $this->get('/lead?country_id=1');
        $response->assertSee($lead->name);
        $response->assertDontSee($lead2->name);

        $response = $this->get('/lead?seller_id='.$user->id);
        $response->assertSee($lead->name);
        $response->assertDontSee($lead2->name);

        $response = $this->get('/lead?status=open');
        $response->assertSee($lead->name);
        $response->assertDontSee($lead2->name);

        $response = $this->get('/lead?industry_id='.$industry->id);
        $response->assertSee($lead->name);
        $response->assertDontSee($lead2->name);
    }
}
