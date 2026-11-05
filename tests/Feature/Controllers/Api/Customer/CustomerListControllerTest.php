<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Customer;

use App\Http\Controllers\Api\ApiController;
use App\Models\Customer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CustomerListControllerTest extends TestCase
{
    #[Test]
    public function it_can_list_customers(): void
    {
        $this->actingAs($this->user, 'api');
        $customers = Customer::factory()->count(2)->create(['company_id' => auth()->user()->company_id, 'seller_id' => auth()->id()]);

        $response = $this->getJson('/api/customer');

        $response->assertOk();
        $response->assertJsonStructure(['count', 'customers', 'total', 'per_page', 'current_page', 'last_page']);
        $response->assertJsonMissingPath('data');
        $this->assertEquals(2, $response->json('count'));
        $this->assertEquals(ApiController::DEFAULT_ITEMS_PER_PAGE, $response->json('per_page'));
        $this->assertEquals(2, $response->json('total'));
        $this->assertEquals($customers[0]->id, $response->json('customers.0.id'));
        $this->assertEquals($customers[1]->id, $response->json('customers.1.id'));
    }

    #[Test]
    public function it_paginates_customers_with_custom_per_page(): void
    {
        $this->actingAs($this->user, 'api');
        Customer::factory()->count(5)->create(['company_id' => auth()->user()->company_id, 'seller_id' => auth()->id()]);

        $response = $this->getJson('/api/customer?per_page=2');

        $response->assertOk();
        $this->assertEquals(2, $response->json('per_page'));
        $this->assertEquals(5, $response->json('total'));
        $this->assertCount(2, $response->json('customers'));
    }

    #[Test]
    public function the_customer_page_links_keep_per_page(): void
    {
        $this->actingAs($this->user, 'api');
        Customer::factory()->count(3)->create(['company_id' => $this->user->company_id, 'seller_id' => $this->user->id]);

        $response = $this->getJson('/api/customer?per_page=2');

        $this->assertStringContainsString('per_page=2', $response->json('next_page_url'));
        $this->assertStringContainsString('page=2', $response->json('next_page_url'));
    }

    #[Test]
    public function it_caps_customers_per_page_at_the_maximum(): void
    {
        $this->actingAs($this->user, 'api');

        $this->getJson('/api/customer?per_page='.(ApiController::MAX_ITEMS_PER_PAGE + 1))->assertOk()->assertJsonPath('per_page', ApiController::MAX_ITEMS_PER_PAGE);
    }

    #[Test]
    public function it_searches_customers_of_the_company_only(): void
    {
        $this->actingAs($this->user, 'api');
        $initech = Customer::factory()->create(['company_id' => $this->user->company_id, 'name' => 'Initech', 'business_name' => null]);
        Customer::factory()->create(['company_id' => $this->user->company_id, 'name' => 'Umbrella', 'business_name' => null]);
        Customer::factory()->create(['company_id' => $this->user->company_id + 1, 'name' => 'Initech', 'business_name' => null]);

        $response = $this->getJson('/api/customer?search=init');

        $response->assertOk()->assertJsonPath('count', 1)->assertJsonPath('customers.0.id', $initech->id);
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $response = $this->getJson('/api/customer');

        $response->assertUnauthorized();
    }
}
