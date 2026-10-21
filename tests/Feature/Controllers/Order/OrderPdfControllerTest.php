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

        $response = $this->get("/order/download/{$order->order_number}");

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function it_blocks_cross_tenant_pdf_download(): void
    {
        $otherOrder = Order::factory()->create();

        $response = $this->get("/order/download/{$otherOrder->order_number}");

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

        $response = $this->get("/order/download/{$order->order_number}");

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    #[Test]
    public function the_pdf_shows_the_full_customer_and_company_address(): void
    {
        $this->user->company->update(['street' => 'Gran Vía 1', 'address_extra' => null, 'locality' => null, 'zipcode' => '28013', 'city' => 'Madrid', 'country_id' => 'es']);
        $customer = Customer::factory()->create([
            'company_id' => $this->user->company_id,
            'country_id' => 'ar',
            'province' => 'Santa Fe',
            'city' => 'Rosario',
            'street' => 'Córdoba 1234',
            'address_extra' => 'Piso 3 B',
            'locality' => null,
            'zipcode' => '2000',
        ]);
        $order = Order::factory()->create([
            'company_id' => $this->user->company_id,
            'customer_id' => $customer->id,
            'seller_id' => $this->user->id,
        ]);

        $html = view('order.pdf', ['order' => $order])->render();

        $this->assertStringContainsString('Córdoba 1234, Piso 3 B, 2000 Rosario, Santa Fe, Argentina', $html);
        $this->assertStringContainsString('Gran Vía 1, 28013 Madrid', $html);
    }

    #[Test]
    public function the_order_view_shows_the_full_customer_address(): void
    {
        $customer = Customer::factory()->create([
            'company_id' => $this->user->company_id,
            'city' => 'Rosario',
            'street' => 'Córdoba 1234',
            'address_extra' => null,
            'locality' => null,
            'zipcode' => '2000',
        ]);
        $order = Order::factory()->create([
            'company_id' => $this->user->company_id,
            'customer_id' => $customer->id,
            'seller_id' => $this->user->id,
        ]);

        $response = $this->get("/order/show/{$order->order_number}");

        $response->assertOk();
        $response->assertSee('Córdoba 1234, 2000 Rosario');
    }
}
