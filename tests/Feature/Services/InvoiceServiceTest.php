<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Order\Item;
use App\Models\Product;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InvoiceServiceTest extends TestCase
{
    use RefreshDatabase;

    private InvoiceService $invoiceService;

    private Company $company;

    private Branch $branch;

    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->invoiceService = app(InvoiceService::class);
        $this->company = Company::factory()->create(['currency' => 'EUR', 'business_name' => 'Issuer S.L.', 'vat' => 'B12345678', 'registration_number' => '12345678']);
        $this->branch = Branch::factory()->forCompany($this->company)->create(['code' => '0007', 'name' => 'Centro']);
        $this->customer = Customer::factory()->create([
            'company_id' => $this->company->id,
            'name' => 'Ana',
            'business_name' => 'García',
            'street' => 'Calle Mayor 1',
            'city' => 'Madrid',
        ]);
    }

    #[Test]
    public function it_copies_the_lines_breaking_down_the_taxes_included_in_the_price(): void
    {
        $product = Product::factory()->create(['company_id' => $this->company->id, 'name' => 'Coffee', 'sku' => 'CF-01', 'barcode' => '4008496982943']);
        $order = $this->createOrder(lines: [['product_id' => $product->id, 'quantity' => 3, 'unit_price' => 10, 'discount' => 10, 'tax' => 21]]);

        $invoice = $this->invoiceService->issue($order);

        $this->assertSame('22.31', $invoice->subtotal);
        $this->assertSame('4.69', $invoice->tax);
        $this->assertSame('27.00', $invoice->total);
        $this->assertSame(Invoice::ISSUED, $invoice->status);
        $this->assertSame('EUR', $invoice->currency);
        $this->assertSame('1.000000', $invoice->fresh()->exchange_rate);

        $line = $invoice->items()->sole();
        $this->assertSame('Coffee', $line->description);
        $this->assertSame('CF-01', $line->sku);
        $this->assertSame('4008496982943', $line->barcode);
        $this->assertSame('3.000', $line->quantity);
        $this->assertSame('22.31', $line->subtotal);
        $this->assertSame('4.69', $line->tax_amount);
        $this->assertSame('27.00', $line->total);
    }

    #[Test]
    public function the_invoice_total_matches_the_order_amount_with_taxes_included(): void
    {
        $order = $this->createOrder(lines: [['quantity' => 1, 'unit_price' => 18.99, 'discount' => 0, 'tax' => 4]]);
        $order->refreshAmount();

        $invoice = $this->invoiceService->issue($order);

        $this->assertSame('18.26', $invoice->subtotal);
        $this->assertSame('0.73', $invoice->tax);
        $this->assertSame('18.99', $invoice->total);
        $this->assertSame((float) $invoice->total, $order->fresh()->getAmount());
    }

    #[Test]
    public function a_line_without_tax_has_no_tax_amount(): void
    {
        $this->assertSame(['subtotal' => '15.00', 'tax_amount' => '0.00', 'total' => '15.00'], InvoiceService::lineTotals('3', '5', '0', '0'));
    }

    #[Test]
    public function it_copies_every_line_with_customer_branch_and_currency(): void
    {
        $order = $this->createOrder(lines: [
            ['quantity' => 1, 'unit_price' => 100, 'discount' => 0, 'tax' => 21],
            ['quantity' => 2, 'unit_price' => 5, 'discount' => 0, 'tax' => 0],
        ]);

        $invoice = $this->invoiceService->issue($order);

        $this->assertCount(2, $invoice->items);
        $this->assertSame($this->customer->id, $invoice->customer_id);
        $this->assertSame($this->branch->id, $invoice->branch_id);
        $this->assertSame($order->id, $invoice->order_id);
        $this->assertSame('110.00', $invoice->total);
    }

    #[Test]
    public function it_completes_the_order(): void
    {
        $order = $this->createOrder();

        $this->invoiceService->issue($order);

        $this->assertSame(Order::COMPLETED, (int) $order->fresh()->status);
        $this->assertTrue($order->fresh()->isInvoiced());
    }

    #[Test]
    public function each_branch_numbers_its_own_invoices(): void
    {
        $otherBranch = Branch::factory()->forCompany($this->company)->create(['code' => '0008']);

        $first = $this->invoiceService->issue($this->createOrder());
        $second = $this->invoiceService->issue($this->createOrder());
        $otherFirst = $this->invoiceService->issue($this->createOrder(branch: $otherBranch));

        $this->assertSame('0007-00000001', $first->fullNumber());
        $this->assertSame('0007-00000002', $second->fullNumber());
        $this->assertSame('0008-00000001', $otherFirst->fullNumber());
        $this->assertSame(2, (int) $this->branch->fresh()->last_invoice_number);
    }

    #[Test]
    public function a_cancelled_invoice_keeps_its_number(): void
    {
        $cancelled = $this->invoiceService->issue($this->createOrder());
        $cancelled->update(['status' => Invoice::CANCELLED, 'cancelled_at' => now()]);

        $next = $this->invoiceService->issue($this->createOrder());

        $this->assertSame(1, $cancelled->fresh()->number);
        $this->assertSame(2, $next->number);
    }

    #[Test]
    public function the_order_of_a_cancelled_invoice_can_be_invoiced_again(): void
    {
        $order = $this->createOrder();
        $this->invoiceService->issue($order)->update(['status' => Invoice::CANCELLED, 'cancelled_at' => now()]);

        $invoice = $this->invoiceService->issue($order);

        $this->assertSame(2, $invoice->number);
    }

    #[Test]
    public function pending_and_canceled_orders_cannot_be_invoiced(): void
    {
        foreach ([Order::PENDING, Order::CANCELED] as $status) {
            $order = $this->createOrder(status: $status);

            try {
                $this->invoiceService->issue($order);
                $this->fail('The order should not be invoiced');
            } catch (ValidationException $exception) {
                $this->assertSame(__('Only confirmed or completed orders can be invoiced'), $exception->errors()['order'][0]);
            }
        }

        $this->assertSame(0, Invoice::count());
        $this->assertSame(0, (int) $this->branch->fresh()->last_invoice_number);
    }

    #[Test]
    public function an_order_with_an_active_invoice_cannot_be_invoiced_again(): void
    {
        $order = $this->createOrder();
        $invoice = $this->invoiceService->issue($order);

        try {
            $this->invoiceService->issue($order);
            $this->fail('The order should not be invoiced twice');
        } catch (ValidationException $exception) {
            $this->assertStringContainsString($invoice->fullNumber(), $exception->errors()['order'][0]);
        }

        $this->assertSame(1, Invoice::count());
    }

    #[Test]
    public function the_snapshot_does_not_change_when_the_customer_changes(): void
    {
        $invoice = $this->invoiceService->issue($this->createOrder());

        $this->customer->update(['street' => 'Gran Vía 2', 'business_name' => 'Pérez']);

        $invoice = $invoice->fresh();
        $this->assertSame('Calle Mayor 1', $invoice->customerAddress()->street);
        $this->assertSame('García', $invoice->customer_data['business_name']);
        $this->assertSame('Issuer S.L.', $invoice->issuer_data['company']['business_name']);
        $this->assertSame('B12345678', $invoice->issuer_data['company']['vat']);
        $this->assertSame('12345678', $invoice->issuer_data['company']['registration_number']);
        $this->assertSame('0007', $invoice->issuer_data['branch']['code']);
        $this->assertSame('Centro', $invoice->issuer_data['branch']['name']);
    }

    #[Test]
    public function a_foreign_currency_invoice_uses_the_given_rate_or_the_company_rate(): void
    {
        $this->company->update(['currency' => 'ARS']);
        $this->company->currencyRates()->create(['currency' => 'USD', 'conversion_rate' => 1200]);

        $withCompanyRate = $this->invoiceService->issue($this->createOrder(currency: 'USD'));
        $withGivenRate = $this->invoiceService->issue($this->createOrder(currency: 'USD'), ['exchange_rate' => '1250.5']);

        $this->assertSame('1200.000000', $withCompanyRate->fresh()->exchange_rate);
        $this->assertSame('1250.500000', $withGivenRate->fresh()->exchange_rate);
        $this->assertSame('USD', $withCompanyRate->currency);
    }

    #[Test]
    public function it_uses_the_given_dates_and_notes(): void
    {
        $invoice = $this->invoiceService->issue($this->createOrder(), [
            'issue_date' => '2026-10-01',
            'due_date' => '2026-10-31',
            'notes' => 'Thanks',
        ]);

        $this->assertSame('2026-10-01', $invoice->fresh()->issue_date->toDateString());
        $this->assertSame('2026-10-31', $invoice->fresh()->due_date->toDateString());
        $this->assertSame('Thanks', $invoice->notes);
    }

    #[Test]
    public function the_due_date_defaults_to_30_days_after_the_issue_date(): void
    {
        $invoice = $this->invoiceService->issue($this->createOrder(), ['issue_date' => '2026-10-01']);

        $this->assertSame('2026-10-31', $invoice->fresh()->due_date->toDateString());
    }

    /**
     * @param  list<array<string, mixed>>  $lines
     */
    private function createOrder(int $status = Order::CONFIRMED, ?Branch $branch = null, string $currency = 'EUR', array $lines = [['quantity' => 1, 'unit_price' => 10, 'discount' => 0, 'tax' => 0]]): Order
    {
        $order = Order::factory()->create([
            'company_id' => $this->company->id,
            'branch_id' => ($branch ?? $this->branch)->id,
            'customer_id' => $this->customer->id,
            'currency' => $currency,
            'status' => $status,
        ]);

        foreach ($lines as $line) {
            Item::factory()->create([
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'product_id' => $line['product_id'] ?? Product::factory()->create(['company_id' => $this->company->id])->id,
                ...array_diff_key($line, ['product_id' => true]),
            ]);
        }

        return $order;
    }
}
