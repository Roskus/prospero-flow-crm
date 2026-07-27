<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Order;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Order;
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
}
