<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Order;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderSaveControllerTest extends TestCase
{
    #[Test]
    public function it_blocks_unauthenticated_order_save(): void
    {
        auth()->guard('web')->logout();

        $response = $this->post('/order/save', []);

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function it_can_save_order(): void
    {
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->post('/order/save', [
            'customer_id' => $customer->id,
            'currency' => 'USD',
        ]);

        $response->assertRedirect('order');
        $this->assertDatabaseHas('order', [
            'customer_id' => $customer->id,
            'company_id' => $this->user->company_id,
        ]);
    }

    #[Test]
    public function it_rejects_updating_order_from_other_company(): void
    {
        $otherCompany = Company::factory()->create();
        $victimOrder = Order::factory()->create([
            'company_id' => $otherCompany->id,
        ]);
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->post('/order/save', [
            'id' => $victimOrder->id,
            'customer_id' => $customer->id,
        ]);

        $response->assertStatus(404);
        $this->assertDatabaseHas('order', [
            'id' => $victimOrder->id,
            'company_id' => $otherCompany->id,
        ]);
    }

    #[Test]
    public function it_accepts_updating_order_from_own_company(): void
    {
        $order = Order::factory()->create([
            'company_id' => $this->user->company_id,
        ]);
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->post('/order/save', [
            'id' => $order->id,
            'customer_id' => $customer->id,
        ]);

        $response->assertRedirect('order');
        $this->assertDatabaseHas('order', [
            'id' => $order->id,
            'company_id' => $this->user->company_id,
            'customer_id' => $customer->id,
        ]);
    }

    #[Test]
    public function it_rejects_customer_from_other_company(): void
    {
        $otherCompany = Company::factory()->create();
        $customer = Customer::factory()->create(['company_id' => $otherCompany->id]);

        $response = $this->post('/order/save', [
            'customer_id' => $customer->id,
        ]);

        $response->assertStatus(404);
    }

    #[Test]
    public function a_new_order_gets_the_next_company_number_on_the_order_and_its_items(): void
    {
        $this->user->company->forceFill(['last_order_number' => 12])->saveQuietly();
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id]);
        $product = Product::factory()->create(['company_id' => $this->user->company_id]);

        $this->post('/order/save', [
            'customer_id' => $customer->id,
            'items' => [['product_id' => $product->id, 'quantity' => 2, 'price' => 10]],
        ])->assertRedirect('order');

        $order = Order::where('company_id', $this->user->company_id)->latest('id')->firstOrFail();
        $this->assertSame(13, (int) $order->order_number);
        $this->assertSame([13], $order->items()->pluck('order_number')->map(fn ($number): int => (int) $number)->all());
        $this->assertSame(13, (int) $this->user->company->fresh()->last_order_number);
    }

    #[Test]
    public function saving_an_existing_order_keeps_its_number(): void
    {
        $order = Order::factory()->create(['company_id' => $this->user->company_id]);
        $number = (int) $order->fresh()->order_number;
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id]);
        $product = Product::factory()->create(['company_id' => $this->user->company_id]);

        $this->post('/order/save', [
            'id' => $order->id,
            'customer_id' => $customer->id,
            'items' => [['product_id' => $product->id, 'quantity' => 1, 'price' => 5]],
        ])->assertRedirect('order');

        $this->assertSame($number, (int) $order->fresh()->order_number);
        $this->assertSame($number, (int) $this->user->company->fresh()->last_order_number);
        $this->assertSame([$number], $order->items()->pluck('order_number')->map(fn ($item): int => (int) $item)->all());
    }
}
