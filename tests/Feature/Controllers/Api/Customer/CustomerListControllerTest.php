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
        $response->assertJsonStructure(['data', 'total', 'per_page', 'current_page', 'last_page']);
        $this->assertEquals(ApiController::DEFAULT_ITEMS_PER_PAGE, $response->json('per_page'));
        $this->assertEquals(2, $response->json('total'));
        $this->assertEquals($customers[0]->id, $response->json('data.0.id'));
        $this->assertEquals($customers[1]->id, $response->json('data.1.id'));
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
        $this->assertCount(2, $response->json('data'));
    }

    #[Test]
    public function it_caps_customers_per_page_at_the_maximum(): void
    {
        $this->actingAs($this->user, 'api');

        $this->getJson('/api/customer?per_page='.(ApiController::MAX_ITEMS_PER_PAGE + 1))->assertOk()->assertJsonPath('per_page', ApiController::MAX_ITEMS_PER_PAGE);
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $response = $this->getJson('/api/customer');

        $response->assertUnauthorized();
    }
}
