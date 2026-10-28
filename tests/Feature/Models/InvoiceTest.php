<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Invoice;
use App\Models\Invoice\Item;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_full_number_is_the_series_and_the_padded_number(): void
    {
        $invoice = Invoice::factory()->make(['series' => '0002', 'number' => 15]);

        $this->assertSame('0002-00000015', $invoice->fullNumber());
    }

    #[Test]
    public function the_balance_is_the_total_minus_the_paid_amount(): void
    {
        $invoice = Invoice::factory()->make(['total' => 500, 'paid_amount' => 300]);

        $this->assertSame('200.00', $invoice->balance());
    }

    #[Test]
    public function an_unpaid_invoice_past_its_due_date_is_overdue(): void
    {
        $this->assertTrue(Invoice::factory()->make(['due_date' => today()->subDay(), 'status' => Invoice::ISSUED])->isOverdue());
        $this->assertTrue(Invoice::factory()->make(['due_date' => today()->subDay(), 'status' => Invoice::PARTIALLY_PAID])->isOverdue());
        $this->assertFalse(Invoice::factory()->make(['due_date' => today(), 'status' => Invoice::ISSUED])->isOverdue());
        $this->assertFalse(Invoice::factory()->make(['due_date' => today()->subDay(), 'status' => Invoice::PAID])->isOverdue());
        $this->assertFalse(Invoice::factory()->make(['due_date' => today()->subDay(), 'status' => Invoice::CANCELLED])->isOverdue());
    }

    #[Test]
    public function the_total_is_converted_to_the_company_currency_with_the_invoice_rate(): void
    {
        $invoice = Invoice::factory()->make(['total' => 100, 'exchange_rate' => 1200]);

        $this->assertSame('120000.00', $invoice->totalInCompanyCurrency());
    }

    #[Test]
    public function an_invoice_has_its_items(): void
    {
        $invoice = Invoice::factory()->create();
        Item::factory()->count(2)->create(['invoice_id' => $invoice->id]);

        $this->assertCount(2, $invoice->items);
        $this->assertTrue($invoice->items()->first()->invoice()->first()->is($invoice));
    }

    #[Test]
    public function an_order_is_invoiced_only_while_its_invoice_is_not_cancelled(): void
    {
        $order = Order::factory()->create(['status' => Order::CONFIRMED]);
        $this->assertFalse($order->isInvoiced());

        $invoice = Invoice::factory()->create(['company_id' => $order->company_id, 'order_id' => $order->id]);
        $this->assertTrue($order->isInvoiced());
        $this->assertSame($invoice->id, $order->activeInvoice->id);

        $invoice->update(['status' => Invoice::CANCELLED, 'cancelled_at' => now()]);
        $this->assertFalse($order->isInvoiced());
        $this->assertNull($order->fresh()->activeInvoice);
    }

    #[Test]
    public function the_addresses_are_built_from_the_snapshots(): void
    {
        $invoice = Invoice::factory()->make([
            'customer_data' => ['name' => 'Ana', 'address' => ['street' => 'Calle Mayor 1', 'city' => 'Madrid']],
            'issuer_data' => ['company' => ['currency' => 'EUR'], 'branch' => ['address' => ['city' => 'Sevilla']]],
        ]);

        $this->assertSame('Calle Mayor 1', $invoice->customerAddress()->street);
        $this->assertSame('Madrid', $invoice->customerAddress()->city);
        $this->assertSame('Sevilla', $invoice->branchAddress()->city);
    }
}
