<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Order;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderCurrencyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->user->company->update(['currency' => 'ARS']);
        $this->user->company->currencyRates()->createMany([
            ['currency' => 'USD', 'conversion_rate' => 1200],
            ['currency' => 'EUR', 'conversion_rate' => 1300],
        ]);
    }

    #[Test]
    public function a_new_order_defaults_to_the_company_currency_with_rate_one(): void
    {
        $this->post('/order/save', ['customer_id' => $this->customer()->id])->assertRedirect('order');

        $order = $this->latestOrder();
        $this->assertSame('ARS', $order->currency);
        $this->assertSame('1.000000', $order->exchange_rate);
    }

    #[Test]
    public function an_order_keeps_its_rate_when_the_company_rate_changes(): void
    {
        $this->post('/order/save', ['customer_id' => $this->customer()->id, 'currency' => 'usd'])->assertRedirect('order');
        $order = $this->latestOrder();

        $this->user->company->currencyRates()->where('currency', 'USD')->update(['conversion_rate' => 1300]);
        $this->post('/order/save', ['id' => $order->id, 'customer_id' => $order->customer_id, 'currency' => 'USD'])->assertRedirect('order');

        $order->refresh();
        $this->assertSame('USD', $order->currency);
        $this->assertSame('1200.000000', $order->exchange_rate);
    }

    #[Test]
    public function changing_the_order_currency_takes_the_current_rate(): void
    {
        $order = Order::factory()->create(['company_id' => $this->user->company_id]);

        $this->post('/order/save', ['id' => $order->id, 'customer_id' => $this->customer()->id, 'currency' => 'EUR'])->assertRedirect('order');

        $this->assertSame('1300.000000', $order->fresh()->exchange_rate);
    }

    #[Test]
    public function a_currency_without_rate_is_a_validation_error(): void
    {
        $this->post('/order/save', ['customer_id' => $this->customer()->id, 'currency' => 'BRL'])
            ->assertSessionHasErrors('currency');
        $this->post('/order/save', ['customer_id' => $this->customer()->id, 'currency' => 'XXY'])
            ->assertSessionHasErrors('currency');

        $this->assertSame(0, Order::where('company_id', $this->user->company_id)->count());
    }

    #[Test]
    public function the_api_creates_orders_in_a_currency_and_returns_the_rate(): void
    {
        $this->actingAs($this->user, 'api');

        $this->postJson('/api/order', ['customer_id' => $this->customer()->id, 'amount' => 10])
            ->assertCreated()
            ->assertJsonPath('order.currency', 'ARS')
            ->assertJsonPath('order.exchange_rate', '1.000000');

        $this->postJson('/api/order', ['customer_id' => $this->customer()->id, 'amount' => 10, 'currency' => 'USD'])
            ->assertCreated()
            ->assertJsonPath('order.currency', 'USD')
            ->assertJsonPath('order.exchange_rate', '1200.000000');

        $this->postJson('/api/order', ['customer_id' => $this->customer()->id, 'amount' => 10, 'currency' => 'BRL'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('currency');
    }

    #[Test]
    public function the_api_updates_the_currency_with_the_current_rate(): void
    {
        $this->actingAs($this->user, 'api');
        $order = Order::factory()->create(['company_id' => $this->user->company_id]);

        $this->putJson('/api/order/'.$order->id, ['currency' => 'USD'])
            ->assertOk()
            ->assertJsonPath('order.exchange_rate', '1200.000000');
    }

    #[Test]
    public function the_order_form_offers_the_company_currency_and_the_rated_ones(): void
    {
        $this->get('/order/create')
            ->assertOk()
            ->assertSee('<option value="ARS" selected>', false)
            ->assertSee('<option value="USD" >', false)
            ->assertDontSee('<option value="BRL"', false);
    }

    #[Test]
    public function the_product_search_converts_prices_to_the_order_currency(): void
    {
        Product::factory()->create(['company_id' => $this->user->company_id, 'name' => 'Teclado', 'price' => 12000, 'currency' => 'ARS', 'tax' => 21]);
        Product::factory()->create(['company_id' => $this->user->company_id, 'name' => 'Teclado BR', 'price' => 50, 'currency' => 'BRL']);

        $rows = collect($this->getJson('/product?term=Teclado&currency=USD', ['X-Requested-With' => 'XMLHttpRequest'])->assertOk()->json())->keyBy('label');

        $this->assertSame('10.00', $rows->first(fn (array $row): bool => str_starts_with($row['label'], 'Teclado ('))['price']);
        $this->assertNull($rows->first(fn (array $row): bool => str_starts_with($row['label'], 'Teclado BR'))['price']);
    }

    #[Test]
    public function the_product_search_without_currency_returns_the_stored_prices(): void
    {
        Product::factory()->create(['company_id' => $this->user->company_id, 'name' => 'Teclado', 'price' => 12000, 'currency' => 'ARS']);

        $this->getJson('/product?term=Teclado', ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertJsonPath('0.label', 'Teclado')
            ->assertJsonPath('0.currency', 'ARS');
    }

    private function customer(): Customer
    {
        return Customer::factory()->create(['company_id' => $this->user->company_id]);
    }

    private function latestOrder(): Order
    {
        return Order::where('company_id', $this->user->company_id)->latest('id')->firstOrFail();
    }
}
