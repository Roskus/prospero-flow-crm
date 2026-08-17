<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OrderPricingValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $seller;

    private Company $company;

    private Product $product;

    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $sellerRole = Role::create(['name' => 'Seller', 'guard_name' => 'web']);
        $sellerRole->givePermissionTo(Permission::findOrCreate('create order', 'web'));
        $sellerRole->givePermissionTo(Permission::findOrCreate('update order', 'web'));
        $sellerRole->givePermissionTo(Permission::findOrCreate('read order', 'web'));

        Role::findOrCreate('SuperAdmin', 'web')->givePermissionTo([
            Permission::findOrCreate('create order', 'web'),
            Permission::findOrCreate('update order', 'web'),
            Permission::findOrCreate('override order price', 'web'),
        ]);

        $this->company = Company::factory()->create();
        $this->seller = User::factory()->create(['company_id' => $this->company->id]);
        $this->seller->assignRole($sellerRole);

        $this->product = Product::factory()->create([
            'company_id' => $this->company->id,
            'price' => 500.00,
        ]);

        $this->customer = Customer::factory()->create([
            'company_id' => $this->company->id,
            'seller_id' => $this->seller->id,
        ]);
    }

    #[Test]
    public function it_derives_unit_price_from_the_product_catalogue_for_sellers(): void
    {
        $this->actingAs($this->seller);

        $this->post('/order/save', $this->orderPayload(price: 0.01))->assertRedirect('order');

        $this->assertSame(500.00, (float) DB::table('order_item')->value('unit_price'));
        $this->assertSame(1, (int) DB::table('order')->count());
    }

    #[Test]
    public function it_derives_unit_price_from_the_catalogue_when_updating_an_order_as_seller(): void
    {
        $this->actingAs($this->seller);

        $order = Order::factory()->create([
            'company_id' => $this->company->id,
            'customer_id' => $this->customer->id,
        ]);
        $order->items()->delete();

        $payload = $this->orderPayload(price: 0.01);
        $payload['id'] = $order->id;

        $this->post('/order/save', $payload)->assertRedirect('order');

        $this->assertSame(500.00, (float) DB::table('order_item')->value('unit_price'));
    }

    #[Test]
    public function it_rejects_a_discount_above_the_limit_for_sellers_without_override(): void
    {
        Event::fake([MessageLogged::class]);
        $this->actingAs($this->seller);

        $response = $this->post('/order/save', $this->orderPayload(price: 500.00, discount: 50));

        $response->assertSessionHasErrors('items.0.discount');
        $this->assertSame(0, (int) DB::table('order')->count());
        $this->assertSame(0, (int) DB::table('order_item')->count());

        Event::assertDispatched(MessageLogged::class, function (MessageLogged $event) {
            return ($event->context['event'] ?? null) === 'order_discount_over_limit'
                && (float) ($event->context['discount'] ?? 0) === 50.0
                && (float) ($event->context['max_discount_percent'] ?? 0) === (float) config('crm.orders.max_discount_percent')
                && ($event->context['user_id'] ?? null) === $this->seller->id;
        });
    }

    #[Test]
    public function it_allows_an_override_user_to_submit_a_custom_price_and_logs_it(): void
    {
        Event::fake([MessageLogged::class]);

        $product = Product::factory()->create([
            'company_id' => $this->user->company_id,
            'price' => 500.00,
        ]);
        $customer = Customer::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $this->user->id,
        ]);

        $this->post('/order/save', [
            'customer_id' => $customer->id,
            'currency' => 'EUR',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'price' => 0.01,
                    'discount' => 0,
                    'tax' => 0,
                ],
            ],
        ])->assertRedirect('order');

        $this->assertSame(0.01, (float) DB::table('order_item')->value('unit_price'));

        Event::assertDispatched(MessageLogged::class, function (MessageLogged $event) use ($product) {
            return ($event->context['event'] ?? null) === 'order_price_override'
                && (float) ($event->context['catalogue_price'] ?? 0) === 500.0
                && (float) ($event->context['overridden_price'] ?? 0) === 0.01
                && ($event->context['product_id'] ?? null) === $product->id;
        });
    }

    #[Test]
    public function it_rejects_a_product_from_another_company(): void
    {
        $this->actingAs($this->seller);

        $otherCompanyProduct = Product::factory()->create(['price' => 10.00]);

        $payload = $this->orderPayload(price: 10.00);
        $payload['items'][0]['product_id'] = $otherCompanyProduct->id;

        $response = $this->post('/order/save', $payload);

        $response->assertSessionHasErrors('items');
        $this->assertSame(0, (int) DB::table('order')->count());
        $this->assertSame(0, (int) DB::table('order_item')->count());
    }

    #[Test]
    public function it_derives_unit_price_from_the_catalogue_when_creating_an_api_item_as_seller(): void
    {
        $this->actingAs($this->seller, 'api');

        $order = Order::factory()->create([
            'company_id' => $this->company->id,
            'customer_id' => $this->customer->id,
        ]);

        $response = $this->postJson('/api/order-item', [
            'order_number' => (string) $order->order_number,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => 0.01,
            'discount' => 0,
            'tax' => 0,
        ]);

        $response->assertCreated();
        $this->assertSame(500.00, (float) DB::table('order_item')->value('unit_price'));
    }

    #[Test]
    public function it_rejects_an_api_item_with_a_product_from_another_company(): void
    {
        $this->actingAs($this->seller, 'api');

        $order = Order::factory()->create([
            'company_id' => $this->company->id,
            'customer_id' => $this->customer->id,
        ]);
        $otherCompanyProduct = Product::factory()->create(['price' => 10.00]);

        $response = $this->postJson('/api/order-item', [
            'order_number' => (string) $order->order_number,
            'product_id' => $otherCompanyProduct->id,
            'quantity' => 1,
            'unit_price' => 10.00,
        ]);

        $response->assertNotFound();
        $this->assertSame(0, (int) DB::table('order_item')->count());
    }

    #[Test]
    public function it_allows_an_override_user_to_set_an_api_item_price_and_logs_it(): void
    {
        Event::fake([MessageLogged::class]);
        $this->actingAs($this->user, 'api');

        $customer = Customer::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $this->user->id,
        ]);
        $order = Order::factory()->create([
            'company_id' => $this->user->company_id,
            'customer_id' => $customer->id,
        ]);
        $product = Product::factory()->create([
            'company_id' => $this->user->company_id,
            'price' => 500.00,
        ]);

        $response = $this->postJson('/api/order-item', [
            'order_number' => (string) $order->order_number,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 0.01,
        ]);

        $response->assertCreated();
        $this->assertSame(0.01, (float) DB::table('order_item')->value('unit_price'));

        Event::assertDispatched(MessageLogged::class, function (MessageLogged $event) use ($product) {
            return ($event->context['event'] ?? null) === 'order_price_override'
                && (float) ($event->context['catalogue_price'] ?? 0) === 500.0
                && (float) ($event->context['overridden_price'] ?? 0) === 0.01
                && ($event->context['product_id'] ?? null) === $product->id;
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function orderPayload(float $price, float $discount = 0.0): array
    {
        return [
            'customer_id' => $this->customer->id,
            'currency' => 'EUR',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                    'price' => $price,
                    'discount' => $discount,
                    'tax' => 0,
                ],
            ],
        ];
    }
}
