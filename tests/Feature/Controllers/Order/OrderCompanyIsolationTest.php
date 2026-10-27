<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Order;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Order\Item;
use App\Models\Product;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Order numbers restart for every company, so two companies have orders with the same number.
 */
class OrderCompanyIsolationTest extends TestCase
{
    #[Test]
    public function an_order_only_has_its_own_items_when_another_company_shares_its_number(): void
    {
        [$ownOrder, $ownProduct] = $this->orderWithItem($this->user->company);
        [$otherOrder, $otherProduct] = $this->orderWithItem(Company::factory()->create());

        $this->assertSame($ownOrder->order_number, $otherOrder->order_number);
        $this->assertSame([$ownProduct->id], $ownOrder->fresh()->items->pluck('product_id')->all());

        $this->get('/order/update/'.$ownOrder->order_number)
            ->assertOk()
            ->assertSee($ownProduct->name)
            ->assertDontSee($otherProduct->name);

        $this->get('/order/show/'.$ownOrder->order_number)
            ->assertOk()
            ->assertDontSee($otherProduct->name);
    }

    #[Test]
    public function editing_an_order_keeps_the_items_of_another_company_with_the_same_number(): void
    {
        [$ownOrder, $ownProduct] = $this->orderWithItem($this->user->company);
        [$otherOrder] = $this->orderWithItem(Company::factory()->create());

        $this->post('/order/save', [
            'id' => $ownOrder->id,
            'customer_id' => $ownOrder->customer_id,
            'items' => [['product_id' => $ownProduct->id, 'quantity' => 2, 'price' => 10]],
        ])->assertRedirect('order');

        $this->assertSame(1, $ownOrder->fresh()->items()->count());
        $this->assertSame(1, $otherOrder->fresh()->items()->count());
    }

    #[Test]
    public function the_sales_report_only_adds_the_amount_of_the_own_items_after_editing(): void
    {
        [$ownOrder, $ownProduct] = $this->orderWithItem($this->user->company);
        [$otherOrder] = $this->orderWithItem(Company::factory()->create());
        $otherOrder->items()->update(['quantity' => 50, 'unit_price' => 30, 'discount' => 0]);

        $this->post('/order/save', [
            'id' => $ownOrder->id,
            'customer_id' => $ownOrder->customer_id,
            'items' => [['product_id' => $ownProduct->id, 'quantity' => 2, 'price' => 10]],
        ])->assertRedirect('order');
        $ownOrder->fresh()->update(['status' => Order::CONFIRMED]);

        $this->assertEquals(20.0, $ownOrder->fresh()->amount);
        $this->get('/report/sale?from='.now()->subDay()->toDateString().'&to='.now()->toDateString())
            ->assertOk()
            ->assertViewHas('report', fn (array $report) => $report['current']['total'] === 20.0 && $report['current']['orders'] === 1);
    }

    #[Test]
    public function an_order_cannot_use_a_product_of_another_company(): void
    {
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id]);
        $otherProduct = Product::factory()->create(['company_id' => Company::factory()->create()->id]);

        $this->post('/order/save', [
            'customer_id' => $customer->id,
            'items' => [['product_id' => $otherProduct->id, 'quantity' => 1, 'price' => 10]],
        ])->assertSessionHasErrors('items.0.product_id');

        $this->assertSame(0, Order::where('customer_id', $customer->id)->count());
    }

    #[Test]
    public function the_api_cannot_add_a_product_of_another_company_to_an_order(): void
    {
        $this->actingAs($this->user, 'api');
        [$ownOrder] = $this->orderWithItem($this->user->company);
        $otherProduct = Product::factory()->create(['company_id' => Company::factory()->create()->id]);

        $this->postJson('/api/order-item', [
            'order_number' => (string) $ownOrder->order_number,
            'product_id' => $otherProduct->id,
            'quantity' => 1,
            'unit_price' => 10,
        ])->assertUnprocessable()->assertJsonValidationErrors('product_id');
    }

    #[Test]
    public function a_deleted_product_cannot_be_added_to_an_order(): void
    {
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id]);
        $deletedProduct = Product::factory()->create(['company_id' => $this->user->company_id]);
        $deletedProduct->delete();

        $this->post('/order/save', [
            'customer_id' => $customer->id,
            'items' => [['product_id' => $deletedProduct->id, 'quantity' => 1, 'price' => 10]],
        ])->assertSessionHasErrors('items.0.product_id');

        $this->actingAs($this->user, 'api');
        [$ownOrder] = $this->orderWithItem($this->user->company);
        $this->postJson('/api/order-item', [
            'order_number' => (string) $ownOrder->order_number,
            'product_id' => $deletedProduct->id,
            'quantity' => 1,
            'unit_price' => 10,
        ])->assertUnprocessable()->assertJsonValidationErrors('product_id');
    }

    #[Test]
    public function an_order_keeps_a_product_deleted_after_it_was_added(): void
    {
        [$ownOrder, $ownProduct] = $this->orderWithItem($this->user->company);
        $ownProduct->delete();

        $this->post('/order/save', [
            'id' => $ownOrder->id,
            'customer_id' => $ownOrder->customer_id,
            'items' => [['product_id' => $ownProduct->id, 'quantity' => 3, 'price' => 10]],
        ])->assertSessionHasNoErrors()->assertRedirect('order');

        $this->assertSame([3], $ownOrder->fresh()->items->pluck('quantity')->all());
    }

    #[Test]
    public function a_deleted_product_of_another_order_cannot_be_reused_when_editing(): void
    {
        [$ownOrder] = $this->orderWithItem($this->user->company);
        [, $productOfOtherOrder] = $this->orderWithItem($this->user->company);
        $productOfOtherOrder->delete();

        $this->post('/order/save', [
            'id' => $ownOrder->id,
            'customer_id' => $ownOrder->customer_id,
            'items' => [['product_id' => $productOfOtherOrder->id, 'quantity' => 1, 'price' => 10]],
        ])->assertSessionHasErrors('items.0.product_id');
    }

    /**
     * @return array{0: Order, 1: Product}
     */
    private function orderWithItem(Company $company): array
    {
        $order = Order::factory()->create([
            'company_id' => $company->id,
            'customer_id' => Customer::factory()->create(['company_id' => $company->id])->id,
            'currency' => $company->currency,
        ]);
        $product = Product::factory()->create(['company_id' => $company->id, 'name' => 'Zzproduct '.$company->id]);
        Item::factory()->create(['order_id' => $order->id, 'order_number' => $order->order_number, 'product_id' => $product->id]);

        return [$order, $product];
    }
}
