<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Lead;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ProspectListRequest;
use App\Models\Industry;
use App\Models\Lead;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
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
        $response->assertJsonStructure(['count', 'leads', 'total', 'per_page', 'current_page', 'last_page']);
        $response->assertJsonMissingPath('data');
        $this->assertSame(2, $response->json('count'));
        $this->assertSame(ApiController::DEFAULT_ITEMS_PER_PAGE, $response->json('per_page'));
        $this->assertSame(2, $response->json('total'));
        $this->assertSame($leads[1]->id, $response->json('leads.0.id'));
        $this->assertSame($leads[0]->id, $response->json('leads.1.id'));
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
        $this->assertSame(3, $response->json('count'));
        $this->assertCount(1, $response->json('leads'));
    }

    #[Test]
    public function the_lead_page_links_keep_per_page(): void
    {
        $this->actingAs($this->user, 'api');
        Industry::factory()->create();
        Lead::factory()->count(3)->create(['company_id' => $this->user->company_id, 'seller_id' => $this->user->id]);

        $response = $this->getJson('/api/lead?per_page=2');

        $this->assertStringContainsString('per_page=2', $response->json('next_page_url'));
        $this->assertStringContainsString('page=2', $response->json('next_page_url'));
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

        $this->getJson('/api/lead')->assertOk()->assertJsonPath('count', 0);
    }

    #[Test]
    public function it_searches_leads_by_name_and_business_name(): void
    {
        $this->actingAs($this->user, 'api');
        Industry::factory()->create();
        $byName = Lead::factory()->create(['company_id' => $this->user->company_id, 'name' => 'Acme Robotics', 'business_name' => null]);
        $byBusinessName = Lead::factory()->create(['company_id' => $this->user->company_id, 'name' => 'Coyote', 'business_name' => 'Acme Industrial S.L.']);
        Lead::factory()->create(['company_id' => $this->user->company_id, 'name' => 'Globex', 'business_name' => null]);

        $response = $this->getJson('/api/lead?search=acme');

        $response->assertOk()->assertJsonPath('count', 2);
        $this->assertEqualsCanonicalizing([$byName->id, $byBusinessName->id], array_column($response->json('leads'), 'id'));
    }

    /**
     * Business rule: any seller finds every lead of the company, whoever its assigned seller is.
     */
    #[Test]
    public function a_seller_finds_leads_of_other_sellers_but_not_of_other_companies(): void
    {
        Industry::factory()->create();
        $seller = User::factory()->create(['company_id' => $this->user->company_id]);
        $seller->assignRole('User');
        $seller->givePermissionTo(Permission::findOrCreate('read lead', 'web'));
        $otherSellersLead = Lead::factory()->create(['company_id' => $this->user->company_id, 'seller_id' => $this->user->id, 'name' => 'Acme']);
        Lead::factory()->create(['company_id' => $this->user->company_id + 1, 'name' => 'Acme']);

        $response = $this->actingAs($seller, 'api')->getJson('/api/lead?search=acme');

        $response->assertOk()->assertJsonPath('count', 1)->assertJsonPath('leads.0.id', $otherSellersLead->id);
    }

    #[Test]
    public function the_lead_page_links_keep_the_search(): void
    {
        $this->actingAs($this->user, 'api');
        Industry::factory()->create();
        Lead::factory()->count(3)->create(['company_id' => $this->user->company_id, 'name' => 'Acme']);

        $next = $this->getJson('/api/lead?search=acme&per_page=2')->json('next_page_url');

        $this->assertStringContainsString('search=acme', $next);
        $this->assertStringContainsString('per_page=2', $next);
    }

    #[Test]
    public function a_too_long_lead_search_is_rejected(): void
    {
        $this->actingAs($this->user, 'api');

        $this->getJson('/api/lead?search='.str_repeat('a', ProspectListRequest::SEARCH_MAX_LENGTH + 1))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['search']);
    }
}
