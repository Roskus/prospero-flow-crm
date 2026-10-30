<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Models\Bank\Account;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\Transaction\Category;
use App\Services\InvoiceService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InvoicePaymentTest extends TestCase
{
    use RefreshDatabase;

    private InvoiceService $invoiceService;

    private Company $company;

    private Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->invoiceService = app(InvoiceService::class);
        $this->company = Company::factory()->create(['currency' => 'EUR']);
        $this->branch = Branch::factory()->forCompany($this->company)->create(['name' => 'Centro']);
    }

    #[Test]
    public function a_payment_is_a_paid_income_transaction_of_the_invoice_customer(): void
    {
        $invoice = $this->createInvoice(500);

        $payment = $this->invoiceService->registerPayment($invoice, ['amount' => 200, 'payment_method' => Transaction::METHOD_CARD, 'reference' => 'TPV-1']);

        $this->assertSame(Transaction::INCOME, $payment->type);
        $this->assertSame(Transaction::PAID, $payment->status);
        $this->assertSame($invoice->customer_id, $payment->customer_id);
        $this->assertSame($invoice->id, $payment->invoice_id);
        $this->assertSame('EUR', $payment->currency);
        $this->assertSame('200.00000', (string) $payment->fresh()->invoice_amount);
        $this->assertStringContainsString($invoice->fullNumber(), $payment->name);
        $this->assertSame('TPV-1', $payment->reference);
    }

    #[Test]
    public function a_payment_is_categorized_as_sales(): void
    {
        $sales = Category::create(['company_id' => $this->company->id, 'name' => Category::SALES]);

        $payment = $this->invoiceService->registerPayment($this->createInvoice(500), ['amount' => 200, 'payment_method' => Transaction::METHOD_CARD]);

        $this->assertSame($sales->id, $payment->transaction_category_id);
        $this->assertSame(1, Category::query()->where('company_id', $this->company->id)->count());
    }

    #[Test]
    public function a_payment_creates_the_sales_category_when_the_company_has_none(): void
    {
        $otherCompanySales = Category::create(['company_id' => Company::factory()->create()->id, 'name' => Category::SALES]);

        $payment = $this->invoiceService->registerPayment($this->createInvoice(500), ['amount' => 200, 'payment_method' => Transaction::METHOD_CARD]);

        $this->assertNotSame($otherCompanySales->id, $payment->transaction_category_id);
        $this->assertSame($this->company->id, $payment->category->company_id);
        $this->assertSame(Category::SALES, $payment->category->name);
    }

    #[Test]
    public function a_partial_payment_leaves_the_invoice_partially_paid(): void
    {
        $invoice = $this->createInvoice(500);

        $this->invoiceService->registerPayment($invoice, ['amount' => 200, 'payment_method' => Transaction::METHOD_CARD]);

        $invoice->refresh();
        $this->assertSame(Invoice::PARTIALLY_PAID, $invoice->status);
        $this->assertSame('300.00', $invoice->balance());
    }

    #[Test]
    public function paying_the_balance_leaves_the_invoice_paid(): void
    {
        $invoice = $this->createInvoice(500);

        $this->invoiceService->registerPayment($invoice, ['amount' => 200, 'payment_method' => Transaction::METHOD_CARD]);
        $this->invoiceService->registerPayment($invoice, ['amount' => 300, 'payment_method' => Transaction::METHOD_CARD]);

        $invoice->refresh();
        $this->assertSame(Invoice::PAID, $invoice->status);
        $this->assertSame('0.00', $invoice->balance());
    }

    #[Test]
    public function a_payment_above_the_balance_is_refused(): void
    {
        $invoice = $this->createInvoice(500);
        $this->invoiceService->registerPayment($invoice, ['amount' => 300, 'payment_method' => Transaction::METHOD_CARD]);

        try {
            $this->invoiceService->registerPayment($invoice, ['amount' => 300, 'payment_method' => Transaction::METHOD_CARD]);
            $this->fail('The payment should be refused');
        } catch (ValidationException $exception) {
            $this->assertStringContainsString('200.00', $exception->errors()['amount'][0]);
        }

        $this->assertSame(1, Transaction::count());
    }

    #[Test]
    public function a_cancelled_invoice_cannot_be_paid(): void
    {
        $invoice = $this->createInvoice(500, ['status' => Invoice::CANCELLED, 'cancelled_at' => now()]);

        $this->expectException(ValidationException::class);

        $this->invoiceService->registerPayment($invoice, ['amount' => 100, 'payment_method' => Transaction::METHOD_CARD]);
    }

    #[Test]
    public function transfer_and_bizum_need_an_account(): void
    {
        $invoice = $this->createInvoice(500);

        foreach ([Transaction::METHOD_TRANSFER, Transaction::METHOD_BIZUM] as $method) {
            try {
                $this->invoiceService->registerPayment($invoice, ['amount' => 100, 'payment_method' => $method]);
                $this->fail('The payment should need an account');
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('bank_account_id', $exception->errors());
            }
        }

        $this->assertSame(0, Transaction::count());
    }

    #[Test]
    public function a_transfer_goes_to_the_chosen_account_with_its_receipt(): void
    {
        Storage::fake('public');
        $invoice = $this->createInvoice(500);
        $account = Account::factory()->create(['company_id' => $this->company->id, 'currency' => 'EUR']);

        $payment = $this->invoiceService->registerPayment($invoice, [
            'amount' => 500,
            'payment_method' => Transaction::METHOD_TRANSFER,
            'bank_account_id' => $account->id,
            'reference' => 'TRF-77',
            'attachment' => UploadedFile::fake()->create('receipt.pdf', 20, 'application/pdf'),
        ]);

        $this->assertSame($account->id, $payment->bank_account_id);
        $this->assertNotNull($payment->attachment);
        Storage::disk('public')->assertExists($payment->attachment);
    }

    #[Test]
    public function a_cash_payment_without_account_goes_to_the_branch_cash_account(): void
    {
        $invoice = $this->createInvoice(500);

        $payment = $this->invoiceService->registerPayment($invoice, ['amount' => 100, 'payment_method' => Transaction::METHOD_CASH]);

        $cash = Account::query()->where('branch_id', $this->branch->id)->where('type', Account::TYPE_CASH)->sole();
        $this->assertSame($cash->id, $payment->bank_account_id);
    }

    #[Test]
    public function an_account_of_another_company_is_not_found(): void
    {
        $invoice = $this->createInvoice(500);
        $foreign = Account::factory()->create(['company_id' => Company::factory()->create()->id]);

        try {
            $this->invoiceService->registerPayment($invoice, ['amount' => 100, 'payment_method' => Transaction::METHOD_TRANSFER, 'bank_account_id' => $foreign->id]);
            $this->fail('The account should not be found');
        } catch (ModelNotFoundException) {
            $this->assertSame(0, Transaction::count());
        }
    }

    #[Test]
    public function a_payment_in_ars_settles_the_usd_invoice_with_both_rates(): void
    {
        $this->company->update(['currency' => 'ARS']);
        $this->company->currencyRates()->create(['currency' => 'USD', 'conversion_rate' => 1200]);
        $invoice = $this->createInvoice(100, ['currency' => 'USD', 'exchange_rate' => 1200]);

        $payment = $this->invoiceService->registerPayment($invoice, ['amount' => 60000, 'currency' => 'ARS', 'payment_method' => Transaction::METHOD_CASH]);

        $this->assertSame('50.00000', (string) $payment->fresh()->invoice_amount);
        $this->assertSame('1.000000', $payment->exchange_rate);
        $this->assertSame('50.00', $invoice->fresh()->balance());
        $this->assertSame(Invoice::PARTIALLY_PAID, $invoice->fresh()->status);
    }

    #[Test]
    public function the_payment_currency_and_rate_default_to_the_invoice_and_company(): void
    {
        $this->company->update(['currency' => 'ARS']);
        $this->company->currencyRates()->create(['currency' => 'USD', 'conversion_rate' => 1250]);
        $invoice = $this->createInvoice(100, ['currency' => 'USD', 'exchange_rate' => 1200]);

        $payment = $this->invoiceService->registerPayment($invoice, ['amount' => 40, 'payment_method' => Transaction::METHOD_CARD]);

        $this->assertSame('USD', $payment->currency);
        $this->assertSame('1250.000000', $payment->exchange_rate);
        $this->assertSame('40.00000', (string) $payment->fresh()->invoice_amount);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createInvoice(float $total, array $attributes = []): Invoice
    {
        return Invoice::factory()->create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'customer_id' => Customer::factory()->create(['company_id' => $this->company->id])->id,
            'subtotal' => $total,
            'tax' => 0,
            'total' => $total,
            ...$attributes,
        ]);
    }
}
