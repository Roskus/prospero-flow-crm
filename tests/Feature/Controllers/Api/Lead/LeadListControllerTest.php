<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Lead;

use App\Http\Controllers\Api\ApiController;
use App\Models\Industry;
use App\Models\Lead;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LeadListControllerTest extends TestCase
{
    #[Test]
    public function it_lists_leads_paginated_newest_first(): void
    {
        $this->actingAs($this->user, 'api');
        Industry::factory()->create();
        $leads = Lead::factory()->count(2)->create(['company_id' => $this->user->company_id, 'seller_id' => $this->user->id]);

        $response = $this->getJson('/api/lead');

        $response->assertOk();
        $response->assertJsonStructure(['data', 'total', 'per_page', 'current_page', 'last_page']);
        $this->assertSame(ApiController::DEFAULT_ITEMS_PER_PAGE, $response->json('per_page'));
        $this->assertSame(2, $response->json('total'));
        $this->assertSame($leads[1]->id, $response->json('data.0.id'));
        $this->assertSame($leads[0]->id, $response->json('data.1.id'));
    }

    #[Test]
    public function it_paginates_leads_with_custom_per_page(): void
    {
        $this->actingAs($this->user, 'api');
        Industry::factory()->create();
        Lead::factory()->count(3)->create(['company_id' => $this->user->company_id, 'seller_id' => $this->user->id]);

        $response = $this->getJson('/api/lead?per_page=2&page=2');

        $response->assertOk();
        $this->assertSame(2, $response->json('per_page'));
        $this->assertSame(3, $response->json('total'));
        $this->assertCount(1, $response->json('data'));
    }

    #[Test]
    public function it_caps_leads_per_page_at_the_maximum(): void
    {
        $this->actingAs($this->user, 'api');

        $this->getJson('/api/lead?per_page='.(ApiController::MAX_ITEMS_PER_PAGE + 1))->assertOk()->assertJsonPath('per_page', ApiController::MAX_ITEMS_PER_PAGE);
        $this->getJson('/api/lead?per_page=0')->assertOk()->assertJsonPath('per_page', 1);
    }

    #[Test]
    public function it_does_not_list_leads_of_another_company(): void
    {
        $this->actingAs($this->user, 'api');
        Industry::factory()->create();
        Lead::factory()->create(['company_id' => $this->user->company_id + 1]);

        $this->getJson('/api/lead')->assertOk()->assertJsonPath('total', 0);
    }
}
