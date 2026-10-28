<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Order;

use App\Exceptions\InvoicedOrderException;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Order\Item;
use App\Models\Product;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InvoicedOrderLockTest extends TestCase
{
    use RefreshDatabase;

    private Order $order;

    private Item $item;

    private Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        $customer = Customer::factory()->create(['company_id' => $this->user->company_id]);
        $this->order = Order::factory()->create([
            'company_id' => $this->user->company_id,
            'customer_id' => $customer->id,
            'status' => Order::CONFIRMED,
            'currency' => $this->user->company->currency,
        ]);
        $this->item = Item::factory()->create([
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'product_id' => Product::factory()->create(['company_id' => $this->user->company_id])->id,
            'quantity' => 2,
            'unit_price' => 10,
            'discount' => 0,
            'tax' => 0,
        ]);
        $this->order->refreshAmount();
        $this->invoice = app(InvoiceService::class)->issue($this->order);
    }

    #[Test]
    public function the_edit_form_of_an_invoiced_order_shows_an_error_naming_the_invoice(): void
    {
        $this->get('/order/update/'.$this->order->order_number)
            ->assertRedirect('order')
            ->assertSessionHas('status', 'error')
            ->assertSessionHas('message', fn (string $message) => str_contains($message, $this->invoice->fullNumber()));
    }

    #[Test]
    public function saving_an_invoiced_order_does_not_change_it(): void
    {
        $this->post('/order/save', [
            'id' => $this->order->id,
            'customer_id' => $this->order->customer_id,
            'order_date' => '2026-01-01',
            'items' => [['product_id' => $this->item->product_id, 'quantity' => 9, 'price' => 99]],
        ])->assertRedirect('order')->assertSessionHas('status', 'error');

        $this->assertSame(2, (int) $this->item->fresh()->quantity);
        $this->assertSame('20.00', number_format((float) $this->order->fresh()->amount, 2));
    }

    #[Test]
    public function an_invoiced_order_cannot_be_deleted_from_the_web(): void
    {
        $this->get('/order/delete/'.$this->order->order_number)->assertSessionHas('status', 'error');

        $this->assertNotSoftDeleted($this->order);
    }

    #[Test]
    public function the_list_hides_the_actions_and_links_the_invoice(): void
    {
        $this->get('/order')
            ->assertOk()
            ->assertSee(route('invoice.show', $this->invoice->id))
            ->assertSee($this->invoice->fullNumber())
            ->assertDontSee("Order.delete({$this->order->order_number},", false)
            ->assertDontSee(route('order.cancel', $this->order->order_number))
            ->assertDontSee('/order/update/'.$this->order->order_number);
    }

    #[Test]
    public function the_api_answers_409_for_an_invoiced_order(): void
    {
        $this->actingAs($this->user, 'api');

        $this->putJson('/api/order/'.$this->order->id, ['status' => Order::PENDING])->assertStatus(409);
        $this->deleteJson('/api/order/'.$this->order->id)->assertStatus(409);
        $this->postJson('/api/order-item', [
            'order_number' => (string) $this->order->order_number,
            'product_id' => $this->item->product_id,
            'quantity' => 1,
            'unit_price' => 5,
        ])->assertStatus(409);
        $this->putJson('/api/order-item/'.$this->item->id, ['quantity' => 5])->assertStatus(409);
        $this->deleteJson('/api/order-item/'.$this->item->id)->assertStatus(409);

        $this->assertSame(Order::COMPLETED, (int) $this->order->fresh()->status);
        $this->assertNotSoftDeleted($this->order);
        $this->assertSame(2, (int) $this->item->fresh()->quantity);
        $this->assertSame(1, $this->order->items()->count());
    }

    #[Test]
    public function the_observer_refuses_changes_to_an_invoiced_order(): void
    {
        $this->expectException(InvoicedOrderException::class);

        $this->order->fresh()->update(['customer_id' => Customer::factory()->create(['company_id' => $this->user->company_id])->id]);
    }

    #[Test]
    public function the_observer_allows_amount_refreshes(): void
    {
        $this->order->fresh()->refreshAmount();

        $this->assertSame('20.00', number_format((float) $this->order->fresh()->amount, 2));
    }

    #[Test]
    public function the_order_can_be_edited_after_cancelling_its_invoice(): void
    {
        app(InvoiceService::class)->cancel($this->invoice);

        $this->get('/order/update/'.$this->order->order_number)->assertOk();
        $this->actingAs($this->user, 'api');
        $this->putJson('/api/order-item/'.$this->item->id, ['quantity' => 5])->assertOk();

        $this->assertSame(5, (int) $this->item->fresh()->quantity);
    }
}
