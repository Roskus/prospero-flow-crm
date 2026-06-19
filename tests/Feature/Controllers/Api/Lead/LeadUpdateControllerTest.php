<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Lead;

use App\Models\Lead;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LeadUpdateControllerTest extends TestCase
{
    #[Test]
    public function it_can_update_lead(): void
    {
        $this->actingAs($this->user, 'api');

        $lead = Lead::factory()->create([
            'seller_id' => $this->user->id,
            'company_id' => $this->user->company_id,
        ]);

        $response = $this->putJson('/api/lead/'.$lead->id, [
            'name' => 'Updated Lead',
            'email' => 'updated@example.com',
            'phone' => '1234567890',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('lead', [
            'id' => $lead->id,
            'name' => 'Updated Lead',
            'email' => 'updated@example.com',
        ]);
    }
}
