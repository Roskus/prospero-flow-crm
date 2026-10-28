<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Order;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderAmountTest extends TestCase
{
    #[Test]
    public function a_new_order_stores_the_total_of_its_lines(): void
    {
        $this->post('/order/save', [
            'customer_id' => $this->customer()->id,
            'items' => [
                ['product_id' => $this->product()->id, 'quantity' => 2, 'price' => 100],
                ['product_id' => $this->product()->id, 'quantity' => 1, 'price' => 50, 'discount' => 10],
            ],
        ])->assertRedirect('order');

        $this->assertEquals(245, $this->latestOrder()->amount);
    }

    #[Test]
    public function editing_the_lines_stores_the_new_total(): void
    {
        $this->post('/order/save', [
            'customer_id' => $this->customer()->id,
            'items' => [['product_id' => $this->product()->id, 'quantity' => 5, 'price' => 100]],
        ]);
        $order = $this->latestOrder();

        $this->post('/order/save', [
            'id' => $order->id,
            'customer_id' => $order->customer_id,
            'items' => [['product_id' => $this->product()->id, 'quantity' => 2, 'price' => 100]],
        ])->assertRedirect('order');

        $this->assertEquals(200, $order->fresh()->amount);
    }

    #[Test]
    public function the_api_item_endpoints_keep_the_amount_up_to_date(): void
    {
        $this->actingAs($this->user, 'api');
        $order = Order::factory()->create(['company_id' => $this->user->company_id, 'amount' => 999]);

        $itemId = $this->postJson('/api/order-item', [
            'order_number' => (string) $order->order_number,
            'product_id' => $this->product()->id,
            'quantity' => 3,
            'unit_price' => 10,
        ])->assertCreated()->json('item.id');
        $this->assertEquals(30, $order->fresh()->amount);

        $this->putJson('/api/order-item/'.$itemId, ['quantity' => 1])->assertOk();
        $this->assertEquals(10, $order->fresh()->amount);

        $this->deleteJson('/api/order-item/'.$itemId)->assertOk();
        $this->assertEquals(0, $order->fresh()->amount);
    }

    #[Test]
    public function a_free_line_can_be_added_to_an_order(): void
    {
        $freeShipping = Product::factory()->create(['company_id' => $this->user->company_id, 'price' => 0]);

        $this->post('/order/save', [
            'customer_id' => $this->customer()->id,
            'items' => [
                ['product_id' => $this->product()->id, 'quantity' => 1, 'price' => 30],
                ['product_id' => $freeShipping->id, 'quantity' => 1, 'price' => 0],
            ],
        ])->assertRedirect('order');

        $order = $this->latestOrder();
        $this->assertEquals(30, $order->amount);
        $this->assertDatabaseHas('order_item', ['order_id' => $order->id, 'product_id' => $freeShipping->id, 'unit_price' => 0]);
    }

    #[Test]
    public function the_api_accepts_a_free_line(): void
    {
        $this->actingAs($this->user, 'api');
        $order = Order::factory()->create(['company_id' => $this->user->company_id]);

        $this->postJson('/api/order-item', [
            'order_number' => (string) $order->order_number,
            'product_id' => $this->product()->id,
            'quantity' => 1,
            'unit_price' => 0,
        ])->assertCreated();

        $this->postJson('/api/order-item', [
            'order_number' => (string) $order->order_number,
            'product_id' => $this->product()->id,
            'quantity' => 1,
            'unit_price' => -1,
        ])->assertUnprocessable()->assertJsonValidationErrors('unit_price');
    }

    private function customer(): Customer
    {
        return Customer::factory()->create(['company_id' => $this->user->company_id]);
    }

    private function product(): Product
    {
        return Product::factory()->create(['company_id' => $this->user->company_id]);
    }

    private function latestOrder(): Order
    {
        return Order::where('company_id', $this->user->company_id)->latest('id')->firstOrFail();
    }
}
