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
        $this->actingAs($this->user, 'api');
        $lead = Lead::factory()->create(['company_id' => $this->user->company_id, 'seller_id' => $this->user->id]);

        $response = $this->deleteJson('/api/lead/'.$lead->id);

        $response->assertOk();
        $this->assertSoftDeleted($lead);
    }

    #[Test]
    public function it_returns_404_for_nonexistent_lead(): void
    {
        $this->actingAs($this->user, 'api');

        $response = $this->deleteJson('/api/lead/99999');

        $response->assertNotFound();
    }

    #[Test]
    public function it_cannot_delete_a_lead_from_another_company(): void
    {
        $this->actingAs($this->user, 'api');
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

    #[Test]
    public function it_denies_user_without_permission(): void
    {
        $userWithoutPermission = User::factory()->create();

        $this->actingAs($userWithoutPermission, 'api');
        $lead = Lead::factory()->create(['company_id' => $userWithoutPermission->company_id, 'seller_id' => $userWithoutPermission->id]);

        $response = $this->deleteJson('/api/lead/'.$lead->id);

        $response->assertStatus(403);
        $this->assertNotSoftDeleted($lead);
    }
}
