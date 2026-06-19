<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Order;

use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderDeleteControllerTest extends TestCase
{
    #[Test]
    public function it_can_delete_an_order(): void
    {
        $this->actingAs($this->user, 'api');
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id, 'seller_id' => $this->user->id]);
        $order = Order::factory()->create(['company_id' => $this->user->company_id, 'customer_id' => $customer->id, 'seller_id' => $this->user->id]);

        $response = $this->deleteJson('/api/order/'.$order->id);

        $response->assertOk();
        $this->assertSoftDeleted($order);
    }

    #[Test]
    public function it_returns_404_for_nonexistent_order(): void
    {
        $this->actingAs($this->user, 'api');

        $response = $this->deleteJson('/api/order/99999');

        $response->assertNotFound();
    }

    #[Test]
    public function it_cannot_delete_an_order_from_another_company(): void
    {
        $this->actingAs($this->user, 'api');
        $otherUser = User::factory()->create();
        $otherCustomer = Customer::factory()->create(['company_id' => $otherUser->company_id, 'seller_id' => $otherUser->id]);
        $otherOrder = Order::factory()->create(['company_id' => $otherUser->company_id, 'customer_id' => $otherCustomer->id, 'seller_id' => $otherUser->id]);

        $response = $this->deleteJson('/api/order/'.$otherOrder->id);

        $response->assertNotFound();
        $this->assertNotSoftDeleted($otherOrder);
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create(['company_id' => $user->company_id, 'seller_id' => $user->id]);
        $order = Order::factory()->create(['company_id' => $user->company_id, 'customer_id' => $customer->id, 'seller_id' => $user->id]);

        $response = $this->deleteJson('/api/order/'.$order->id);

        $response->assertUnauthorized();
    }

    #[Test]
    public function it_denies_user_without_permission(): void
    {
        $userWithoutPermission = User::factory()->create();

        $this->actingAs($userWithoutPermission, 'api');
        $customer = Customer::factory()->create(['company_id' => $userWithoutPermission->company_id, 'seller_id' => $userWithoutPermission->id]);
        $order = Order::factory()->create(['company_id' => $userWithoutPermission->company_id, 'customer_id' => $customer->id, 'seller_id' => $userWithoutPermission->id]);

        $response = $this->deleteJson('/api/order/'.$order->id);

        $response->assertStatus(403);
        $this->assertNotSoftDeleted($order);
    }
}
