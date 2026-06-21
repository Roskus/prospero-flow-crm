<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Customer;

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
        $this->assertEquals(20, $response->json('per_page'));
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
    public function it_requires_authentication(): void
    {
        $response = $this->getJson('/api/customer');

        $response->assertUnauthorized();
    }
}
