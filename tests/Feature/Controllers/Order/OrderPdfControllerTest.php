<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Order;

use App\Models\Customer;
use App\Models\Order;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderPdfControllerTest extends TestCase
{
    #[Test]
    public function it_blocks_unauthenticated_pdf_download(): void
    {
        $order = Order::factory()->create(['company_id' => $this->user->company_id]);

        auth()->guard('web')->logout();

        $response = $this->get("/order/download/{$order->id}");

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_blocks_cross_tenant_pdf_download(): void
    {
        $otherOrder = Order::factory()->create();

        $response = $this->get("/order/download/{$otherOrder->id}");

        $response->assertNotFound();
    }

    #[Test]
    public function it_can_download_own_order_pdf(): void
    {
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id]);
        $order = Order::factory()->create([
            'company_id' => $this->user->company_id,
            'customer_id' => $customer->id,
            'seller_id' => $this->user->id,
        ]);

        $response = $this->get("/order/download/{$order->id}");

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }
}
