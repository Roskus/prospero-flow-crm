<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Lead;

use App\Models\Lead;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LeadDeleteControllerTest extends TestCase
{
    #[Test]
    public function it_can_delete_a_lead(): void
    {
        $this->actingAs(User::factory()->create(), 'api');
        $lead = Lead::factory()->create(['company_id' => auth()->user()->company_id, 'seller_id' => auth()->id()]);

        $response = $this->deleteJson('/api/lead/'.$lead->id);

        $response->assertOk();
        $this->assertSoftDeleted($lead);
    }

    #[Test]
    public function it_returns_404_for_nonexistent_lead(): void
    {
        $this->actingAs(User::factory()->create(), 'api');

        $response = $this->deleteJson('/api/lead/99999');

        $response->assertNotFound();
    }

    #[Test]
    public function it_cannot_delete_a_lead_from_another_company(): void
    {
        $this->actingAs(User::factory()->create(), 'api');
        $otherUser = User::factory()->create();
        $otherLead = Lead::factory()->create(['company_id' => $otherUser->company_id, 'seller_id' => $otherUser->id]);

        $response = $this->deleteJson('/api/lead/'.$otherLead->id);

        $response->assertNotFound();
        $this->assertNotSoftDeleted($otherLead);
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $lead = Lead::factory()->create();

        $response = $this->deleteJson('/api/lead/'.$lead->id);

        $response->assertUnauthorized();
    }
}
