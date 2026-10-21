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

    #[Test]
    public function updating_without_address_fields_keeps_the_address(): void
    {
        $this->actingAs($this->user, 'api');

        $lead = Lead::factory()->create([
            'seller_id' => $this->user->id,
            'company_id' => $this->user->company_id,
            'country_id' => 'ar',
            'city' => 'Rosario',
            'street' => 'Córdoba 1234',
        ]);

        $this->putJson('/api/lead/'.$lead->id, ['name' => 'Updated Lead'])->assertOk();

        $this->assertDatabaseHas('lead', [
            'id' => $lead->id,
            'country_id' => 'ar',
            'city' => 'Rosario',
            'street' => 'Córdoba 1234',
        ]);
    }

    #[Test]
    public function a_partial_update_only_changes_the_fields_sent(): void
    {
        $this->actingAs($this->user, 'api');

        $lead = Lead::factory()->create([
            'seller_id' => $this->user->id,
            'company_id' => $this->user->company_id,
            'vat' => 'B12345678',
            'phone' => '600111222',
            'status' => Lead::IN_PROGRESS,
            'notes' => 'Keep me',
        ]);

        $this->putJson('/api/lead/'.$lead->id, ['business_name' => 'New Business', 'mobile' => '+34 611 222 333'])
            ->assertOk()
            ->assertJsonPath('lead.business_name', 'New Business');

        $this->assertDatabaseHas('lead', [
            'id' => $lead->id,
            'name' => $lead->name,
            'business_name' => 'New Business',
            'mobile' => '34611222333',
            'vat' => 'B12345678',
            'phone' => '600111222',
            'status' => Lead::IN_PROGRESS,
            'notes' => 'Keep me',
            'seller_id' => $this->user->id,
        ]);
    }

    #[Test]
    public function it_updates_the_address_fields_sent(): void
    {
        $this->actingAs($this->user, 'api');

        $lead = Lead::factory()->create([
            'seller_id' => $this->user->id,
            'company_id' => $this->user->company_id,
            'country_id' => 'ar',
            'city' => 'Rosario',
        ]);

        $this->putJson('/api/lead/'.$lead->id, ['country_id' => 'ES', 'street' => 'Calle Mayor 1'])->assertOk();

        $this->assertDatabaseHas('lead', [
            'id' => $lead->id,
            'country_id' => 'es',
            'city' => 'Rosario',
            'street' => 'Calle Mayor 1',
        ]);
    }

    #[Test]
    public function it_rejects_an_invalid_or_empty_country_and_name(): void
    {
        $this->actingAs($this->user, 'api');

        $lead = Lead::factory()->create([
            'seller_id' => $this->user->id,
            'company_id' => $this->user->company_id,
        ]);

        $this->putJson('/api/lead/'.$lead->id, ['country_id' => 'zz', 'name' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['country_id', 'name']);

        $this->putJson('/api/lead/'.$lead->id, ['country_id' => null])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['country_id']);
    }
}
