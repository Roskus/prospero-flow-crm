<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Order;

use App\Http\Controllers\Api\ApiController;
use App\Models\Customer;
use App\Models\Order;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderListControllerTest extends TestCase
{
    #[Test]
    public function it_lists_orders_paginated_newest_first(): void
    {
        $this->actingAs($this->user, 'api');
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id]);
        $orders = Order::factory()->count(2)->create(['company_id' => $this->user->company_id, 'customer_id' => $customer->id, 'seller_id' => $this->user->id]);

        $response = $this->getJson('/api/order');

        $response->assertOk();
        $response->assertJsonStructure(['count', 'orders', 'total', 'per_page', 'current_page', 'last_page']);
        $response->assertJsonMissingPath('data');
        $this->assertSame(2, $response->json('count'));
        $this->assertSame(ApiController::DEFAULT_ITEMS_PER_PAGE, $response->json('per_page'));
        $this->assertSame(2, $response->json('total'));
        $this->assertSame($orders[1]->id, $response->json('orders.0.id'));
        $this->assertSame($orders[0]->id, $response->json('orders.1.id'));
    }

    #[Test]
    public function it_paginates_orders_with_custom_per_page(): void
    {
        $this->actingAs($this->user, 'api');
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id]);
        Order::factory()->count(3)->create(['company_id' => $this->user->company_id, 'customer_id' => $customer->id, 'seller_id' => $this->user->id]);

        $response = $this->getJson('/api/order?per_page=2&page=2');

        $response->assertOk();
        $this->assertSame(2, $response->json('per_page'));
        $this->assertSame(3, $response->json('total'));
        $this->assertSame(3, $response->json('count'));
        $this->assertCount(1, $response->json('orders'));
    }

    #[Test]
    public function it_caps_orders_per_page_at_the_maximum(): void
    {
        $this->actingAs($this->user, 'api');

        $this->getJson('/api/order?per_page='.(ApiController::MAX_ITEMS_PER_PAGE + 1))->assertOk()->assertJsonPath('per_page', ApiController::MAX_ITEMS_PER_PAGE);
        $this->getJson('/api/order?per_page=0')->assertOk()->assertJsonPath('per_page', 1);
    }

    #[Test]
    public function it_does_not_list_orders_of_another_company(): void
    {
        $this->actingAs($this->user, 'api');
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id]);
        Order::factory()->create(['company_id' => $this->user->company_id + 1, 'customer_id' => $customer->id, 'seller_id' => $this->user->id]);

        $this->getJson('/api/order')->assertOk()->assertJsonPath('count', 0);
    }
}
