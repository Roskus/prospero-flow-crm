<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Invoice;

use App\Models\Bank\Account;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Order\Item;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Services\InvoiceService;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class InvoicePaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user->company->update(['currency' => 'EUR']);
    }

    #[Test]
    public function paying_at_invoicing_time_creates_a_paid_invoice_and_the_cash_payment(): void
    {
        $order = $this->createOrder(500);

        $this->post(route('invoice.save'), [
            'order_id' => $order->id,
            'issue_date' => '2026-10-28',
            'due_date' => '2026-11-27',
            'register_payment' => '1',
            'payment' => ['payment_method' => Transaction::METHOD_CASH, 'amount' => 500],
        ])->assertRedirect();

        $invoice = Invoice::sole();
        $this->assertSame(Invoice::PAID, $invoice->status);
        $payment = Transaction::sole();
        $this->assertSame($invoice->id, $payment->invoice_id);
        $this->assertSame(Account::TYPE_CASH, $payment->bankAccount->type);
    }

    #[Test]
    public function the_invoice_is_not_created_when_the_payment_at_invoicing_time_is_invalid(): void
    {
        $order = $this->createOrder(500);

        $this->post(route('invoice.save'), [
            'order_id' => $order->id,
            'issue_date' => '2026-10-28',
            'due_date' => '2026-11-27',
            'register_payment' => '1',
            'payment' => ['payment_method' => Transaction::METHOD_TRANSFER, 'amount' => 500],
        ])->assertSessionHasErrors('payment.bank_account_id');

        $this->assertSame(0, Invoice::count());
        $this->assertSame(0, Transaction::count());
        $this->assertSame(Order::CONFIRMED, (int) $order->fresh()->status);
    }

    #[Test]
    public function the_payment_section_is_ignored_when_not_checked(): void
    {
        $order = $this->createOrder(500);

        $this->post(route('invoice.save'), [
            'order_id' => $order->id,
            'issue_date' => '2026-10-28',
            'due_date' => '2026-11-27',
            'register_payment' => '0',
            'payment' => ['payment_method' => 'invalid', 'amount' => ''],
        ])->assertSessionHasNoErrors();

        $this->assertSame(Invoice::ISSUED, Invoice::sole()->status);
        $this->assertSame(0, Transaction::count());
    }

    #[Test]
    public function a_transfer_with_a_receipt_is_registered_from_the_invoice_page(): void
    {
        Storage::fake('public');
        $invoice = $this->issue(500);
        $account = Account::factory()->create(['company_id' => $this->user->company_id, 'currency' => 'EUR', 'account_name' => 'Main bank']);

        $this->post(route('invoice.payment', $invoice->id), [
            'payment_method' => Transaction::METHOD_TRANSFER,
            'amount' => 200,
            'bank_account_id' => $account->id,
            'reference' => 'TRF-42',
            'attachment' => UploadedFile::fake()->create('receipt.pdf', 30, 'application/pdf'),
        ])->assertRedirect(route('invoice.show', $invoice->id))->assertSessionHas('status', 'success');

        $payment = Transaction::sole();
        Storage::disk('public')->assertExists($payment->attachment);
        $this->assertSame(Invoice::PARTIALLY_PAID, $invoice->fresh()->status);

        $this->get(route('invoice.show', $invoice->id))
            ->assertSee('TRF-42')
            ->assertSee('Main bank')
            ->assertSee(basename($payment->attachment))
            ->assertSee('value="300.00"', false);
    }

    #[Test]
    public function a_payment_above_the_balance_shows_the_balance(): void
    {
        $invoice = $this->issue(500);

        $this->post(route('invoice.payment', $invoice->id), ['payment_method' => Transaction::METHOD_CASH, 'amount' => 600])
            ->assertSessionHasErrors(['amount']);

        $this->assertSame(0, Transaction::count());
    }

    #[Test]
    public function a_payment_to_an_account_of_another_company_returns_not_found(): void
    {
        $invoice = $this->issue(500);
        $foreign = Account::factory()->create(['company_id' => Company::factory()->create()->id]);

        $this->post(route('invoice.payment', $invoice->id), ['payment_method' => Transaction::METHOD_TRANSFER, 'amount' => 100, 'bank_account_id' => $foreign->id])
            ->assertNotFound();

        $this->assertSame(0, Transaction::count());
    }

    #[Test]
    public function the_invoice_of_another_company_cannot_be_paid(): void
    {
        $invoice = Invoice::factory()->create();

        $this->post(route('invoice.payment', $invoice->id), ['payment_method' => Transaction::METHOD_CASH, 'amount' => 10])->assertNotFound();
    }

    #[Test]
    public function the_payment_form_is_hidden_on_paid_and_cancelled_invoices(): void
    {
        $paid = $this->issue(100);
        app(InvoiceService::class)->registerPayment($paid, ['payment_method' => Transaction::METHOD_CASH, 'amount' => 100]);

        $this->get(route('invoice.show', $paid->id))->assertDontSee('id="payment-form"', false)->assertSee(__('Paid in full'));
    }

    #[Test]
    public function the_pay_action_is_shown_only_for_orders_with_an_unpaid_invoice(): void
    {
        $unpaid = $this->issue(100);
        $paid = $this->issue(100);
        app(InvoiceService::class)->registerPayment($paid, ['payment_method' => Transaction::METHOD_CASH, 'amount' => 100]);
        $notInvoiced = $this->createOrder(100);

        $response = $this->get('/order');

        $response->assertSee(route('invoice.show', $unpaid->id).'#payment-form', false);
        $response->assertDontSee(route('invoice.show', $paid->id).'#payment-form', false);
        $this->get('/order/show/'.$unpaid->order->order_number)->assertSee(route('invoice.show', $unpaid->id).'#payment-form', false);
        $this->assertFalse($notInvoiced->isInvoiced());
    }

    #[Test]
    public function the_payment_section_is_hidden_without_create_payment(): void
    {
        $user = User::factory()->create(['company_id' => $this->user->company_id]);
        $user->givePermissionTo([Permission::findOrCreate('create invoice', 'web'), Permission::findOrCreate('read invoice', 'web')]);
        $order = $this->createOrder(100);

        $this->actingAs($user)->get(route('invoice.create', $order->order_number))
            ->assertOk()
            ->assertDontSee('name="register_payment"', false);

        $this->actingAs($user)->post(route('invoice.save'), [
            'order_id' => $order->id,
            'issue_date' => '2026-10-28',
            'due_date' => '2026-10-28',
            'register_payment' => '1',
            'payment' => ['payment_method' => Transaction::METHOD_CASH, 'amount' => 100],
        ]);

        $this->assertSame(1, Invoice::count());
        $this->assertSame(0, Transaction::count());
        $this->actingAs($user)->post(route('invoice.payment', Invoice::sole()->id), ['payment_method' => Transaction::METHOD_CASH, 'amount' => 10])->assertForbidden();
    }

    #[Test]
    public function a_seller_collects_at_invoicing_time_but_cannot_open_accounting(): void
    {
        $this->seed([RoleSeeder::class, PermissionSeeder::class]);
        $seller = User::factory()->create(['company_id' => $this->user->company_id]);
        $seller->assignRole('Seller');
        $order = $this->createOrder(100);

        $this->actingAs($seller)->post(route('invoice.save'), [
            'order_id' => $order->id,
            'issue_date' => '2026-10-28',
            'due_date' => '2026-10-28',
            'register_payment' => '1',
            'payment' => ['payment_method' => Transaction::METHOD_CASH, 'amount' => 100],
        ])->assertRedirect();

        $this->assertSame(Invoice::PAID, Invoice::sole()->status);
        $this->assertSame(1, Transaction::count());
        $this->actingAs($seller)->get('/accounting')->assertForbidden();
    }

    #[Test]
    public function a_seller_invoices_and_filters_customers_assigned_to_another_seller(): void
    {
        $this->seed([RoleSeeder::class, PermissionSeeder::class]);
        $seller = User::factory()->create(['company_id' => $this->user->company_id]);
        $seller->assignRole('Seller');
        $otherSeller = User::factory()->create(['company_id' => $this->user->company_id]);
        $order = $this->createOrder(100);
        $customer = Customer::withoutGlobalScopes()->findOrFail($order->customer_id);
        $customer->forceFill(['seller_id' => $otherSeller->id, 'name' => 'Cliente ajeno'])->saveQuietly();

        $this->actingAs($seller)->post(route('invoice.save'), [
            'order_id' => $order->id,
            'issue_date' => '2026-10-28',
            'due_date' => '2026-10-28',
        ])->assertRedirect();

        $invoice = Invoice::sole();
        $this->assertSame($customer->id, $invoice->customer_id);
        $this->assertSame('Cliente ajeno', $invoice->customer_data['name']);

        $this->actingAs($seller)->get(route('invoice.index'))
            ->assertOk()
            ->assertSee('<option value="'.$customer->id.'"', false);
    }

    private function createOrder(float $total): Order
    {
        $order = Order::factory()->create([
            'company_id' => $this->user->company_id,
            'branch_id' => Branch::ofCompany($this->user->company_id)->where('is_main', true)->value('id'),
            'customer_id' => Customer::factory()->create(['company_id' => $this->user->company_id])->id,
            'currency' => 'EUR',
            'status' => Order::CONFIRMED,
        ]);
        Item::factory()->create([
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'product_id' => Product::factory()->create(['company_id' => $this->user->company_id])->id,
            'quantity' => 1,
            'unit_price' => $total,
            'discount' => 0,
            'tax' => 0,
        ]);

        return $order;
    }

    private function issue(float $total): Invoice
    {
        return app(InvoiceService::class)->issue($this->createOrder($total));
    }
}
