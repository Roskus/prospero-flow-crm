<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Order;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Order\Item;
use App\Models\Product;
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

    #[Test]
    public function the_order_view_shows_both_sku_and_barcode(): void
    {
        $order = $this->createOrderWithProducts();

        $response = $this->get("/order/show/{$order->order_number}");

        $response->assertOk();
        $response->assertSeeText('SKU/EAN');
        $response->assertSee('<td>SKU-001<br>9990000000001</td>', false);
        $response->assertSee('<td>8412345678905</td>', false);
    }

    #[Test]
    public function the_pdf_shows_both_sku_and_barcode(): void
    {
        $order = $this->createOrderWithProducts();

        $html = view('order.pdf', ['order' => $order])->render();

        $this->assertStringContainsString('SKU/EAN', $html);
        $this->assertStringContainsString('<td>SKU-001<br>9990000000001</td>', $html);
        $this->assertStringContainsString('<td>8412345678905</td>', $html);
    }

    #[Test]
    public function a_zero_sku_or_barcode_is_shown_as_a_code(): void
    {
        $order = Order::factory()->create([
            'company_id' => $this->user->company_id,
            'customer_id' => Customer::factory()->create(['company_id' => $this->user->company_id])->id,
            'seller_id' => $this->user->id,
        ]);
        $product = Product::factory()->create([
            'company_id' => $this->user->company_id,
            'sku' => '0',
            'barcode' => '0',
        ]);
        Item::factory()->create(['order_id' => $order->id, 'order_number' => $order->order_number, 'product_id' => $product->id]);

        $this->get("/order/show/{$order->order_number}")->assertOk()->assertSee('<td>0<br>0</td>', false);
        $this->assertStringContainsString('<td>0<br>0</td>', view('order.pdf', ['order' => $order->fresh()])->render());
    }

    private function createOrderWithProducts(): Order
    {
        $order = Order::factory()->create([
            'company_id' => $this->user->company_id,
            'customer_id' => Customer::factory()->create(['company_id' => $this->user->company_id])->id,
            'seller_id' => $this->user->id,
        ]);
        $productWithSku = Product::factory()->create([
            'company_id' => $this->user->company_id,
            'sku' => 'SKU-001',
            'barcode' => '9990000000001',
        ]);
        $productWithoutSku = Product::factory()->create([
            'company_id' => $this->user->company_id,
            'sku' => null,
            'barcode' => '8412345678905',
        ]);
        Item::factory()->create(['order_id' => $order->id, 'order_number' => $order->order_number, 'product_id' => $productWithSku->id]);
        Item::factory()->create(['order_id' => $order->id, 'order_number' => $order->order_number, 'product_id' => $productWithoutSku->id]);

        return $order->fresh();
    }
}
