<?php

namespace Tests\Feature\Controllers\Lead;

use App\Models\Lead;
use App\Models\User;
use Tests\TestCase;

class LeadDeleteControllerTest extends TestCase
{
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_delete_lead()
    {
        $lead = Lead::factory()->create();

        $response = $this->get('/lead/delete/'.$lead->id);

        $response->assertRedirect('/lead');
        $response->assertSessionHas('message', 'Lead deleted successfully');
        $this->assertDatabaseMissing('lead', $lead->toArray());
    }
}
