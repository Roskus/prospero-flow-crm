<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Transaction;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InvoicePaymentStatusTest extends TestCase
{
    use RefreshDatabase;

    private Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user->company->update(['currency' => 'EUR']);
        $this->invoice = Invoice::factory()->create([
            'company_id' => $this->user->company_id,
            'branch_id' => Branch::ofCompany($this->user->company_id)->where('is_main', true)->value('id'),
            'customer_id' => Customer::factory()->create(['company_id' => $this->user->company_id])->id,
            'subtotal' => 500,
            'tax' => 0,
            'total' => 500,
        ]);
    }

    #[Test]
    public function deleting_the_only_payment_from_the_accounting_screen_issues_the_invoice_again(): void
    {
        $payment = $this->pay(500);
        $this->assertSame(Invoice::PAID, $this->invoice->fresh()->status);

        $this->delete('/transaction/delete/'.$payment->id);

        $this->assertSoftDeleted($payment);
        $this->assertSame(Invoice::ISSUED, $this->invoice->fresh()->status);
        $this->assertSame('500.00', $this->invoice->fresh()->balance());
    }

    #[Test]
    public function restoring_a_payment_pays_the_invoice_again(): void
    {
        $payment = $this->pay(500);
        $payment->delete();

        $payment->restore();

        $this->assertSame(Invoice::PAID, $this->invoice->fresh()->status);
    }

    #[Test]
    public function editing_a_payment_amount_from_the_accounting_screen_refreshes_the_invoice(): void
    {
        $payment = $this->pay(500);

        $this->post('/transaction/save', [
            'id' => $payment->id,
            'name' => $payment->name,
            'type' => Transaction::INCOME,
            'amount' => 400,
            'issue_date' => $payment->issue_date->toDateString(),
            'status' => Transaction::PAID,
            'payment_method' => Transaction::METHOD_CARD,
        ])->assertRedirect('accounting');

        $this->assertSame('400.00000', (string) $payment->fresh()->invoice_amount);
        $this->assertSame(Invoice::PARTIALLY_PAID, $this->invoice->fresh()->status);
        $this->assertSame('100.00', $this->invoice->fresh()->balance());
    }

    #[Test]
    public function editing_a_payment_amount_from_the_api_refreshes_the_invoice(): void
    {
        $payment = $this->pay(500);
        $this->actingAs($this->user, 'api');

        $this->putJson('/api/transaction/'.$payment->id, ['amount' => 200])->assertOk();

        $this->assertSame(Invoice::PARTIALLY_PAID, $this->invoice->fresh()->status);
        $this->assertSame('300.00', $this->invoice->fresh()->balance());
    }

    #[Test]
    public function deleting_a_payment_from_the_api_refreshes_the_invoice(): void
    {
        $this->pay(200);
        $payment = $this->pay(300);
        $this->actingAs($this->user, 'api');

        $this->deleteJson('/api/transaction/'.$payment->id)->assertOk();

        $this->assertSame(Invoice::PARTIALLY_PAID, $this->invoice->fresh()->status);
        $this->assertSame('300.00', $this->invoice->fresh()->balance());
    }

    #[Test]
    public function a_payment_marked_pending_does_not_count(): void
    {
        $payment = $this->pay(500);

        $payment->update(['status' => Transaction::PENDING]);

        $this->assertSame(Invoice::ISSUED, $this->invoice->fresh()->status);
    }

    private function pay(float $amount): Transaction
    {
        return app(InvoiceService::class)->registerPayment($this->invoice, ['amount' => $amount, 'payment_method' => Transaction::METHOD_CARD]);
    }
}
