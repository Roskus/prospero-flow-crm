<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Invoice;

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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use SplFileInfo;
use Tests\TestCase;

class InvoiceControllerTest extends TestCase
{
    use RefreshDatabase;

    private Branch $branch;

    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user->company->update(['currency' => 'EUR']);
        $this->branch = Branch::ofCompany($this->user->company_id)->where('is_main', true)->firstOrFail();
        $this->customer = Customer::factory()->create(['company_id' => $this->user->company_id, 'name' => 'Ana', 'business_name' => 'García']);
    }

    #[Test]
    public function the_create_invoice_action_is_shown_only_on_confirmed_orders_without_an_active_invoice(): void
    {
        $confirmed = $this->createOrder(Order::CONFIRMED);
        $pending = $this->createOrder(Order::PENDING);
        $canceled = $this->createOrder(Order::CANCELED);
        $invoiced = $this->createOrder(Order::CONFIRMED);
        app(InvoiceService::class)->issue($invoiced);

        $response = $this->get('/order');

        $response->assertOk();
        $response->assertSee(route('invoice.create', $confirmed->order_number));
        $response->assertDontSee(route('invoice.create', $pending->order_number));
        $response->assertDontSee(route('invoice.create', $canceled->order_number));
        $response->assertDontSee(route('invoice.create', $invoiced->order_number));
    }

    #[Test]
    public function the_create_invoice_action_is_shown_on_the_order_page(): void
    {
        $order = $this->createOrder(Order::CONFIRMED);

        $this->get('/order/show/'.$order->order_number)->assertSee(route('invoice.create', $order->order_number));
    }

    #[Test]
    public function the_invoice_form_is_prefilled(): void
    {
        $order = $this->createOrder(Order::CONFIRMED);

        $response = $this->get(route('invoice.create', $order->order_number));

        $response->assertOk();
        $response->assertSee('name="issue_date"', false);
        $response->assertSee('value="'.today()->toDateString().'"', false);
        $response->assertSee('value="'.today()->addDays(30)->toDateString().'"', false);
        $response->assertDontSee('name="exchange_rate"', false);
    }

    #[Test]
    public function the_invoice_form_shows_the_order_date_and_breaks_down_the_taxes_included(): void
    {
        $order = $this->createOrder(Order::CONFIRMED);
        $order->update(['order_date' => '2024-12-02']);
        $order->items()->sole()->product->update(['sku' => 'PB-01', 'barcode' => '5012345678900']);

        $response = $this->get(route('invoice.create', $order->order_number));

        $response->assertOk();
        $response->assertSee('02/12/2024');
        $response->assertSee('data-date="2024-12-02"', false);
        $response->assertSee('PB-01<br>5012345678900', false);
        $response->assertSeeInOrder([__('Subtotal (without taxes)'), '82.64', __('Taxes included'), '17.36', __('Total'), '100.00']);
        $response->assertSee('value="100.00"', false);
    }

    #[Test]
    public function the_invoice_form_prefills_the_company_rate_for_a_foreign_currency(): void
    {
        $this->user->company->currencyRates()->create(['currency' => 'USD', 'conversion_rate' => 1.1]);
        $order = $this->createOrder(Order::CONFIRMED, currency: 'USD');

        $response = $this->get(route('invoice.create', $order->order_number));

        $response->assertSee('name="exchange_rate"', false);
        $response->assertSee('value="1.100000"', false);
    }

    #[Test]
    public function a_pending_order_cannot_open_the_invoice_form(): void
    {
        $order = $this->createOrder(Order::PENDING);

        $this->get(route('invoice.create', $order->order_number))
            ->assertRedirect('order')
            ->assertSessionHas('status', 'error');
    }

    #[Test]
    public function an_invoice_is_created_from_the_form(): void
    {
        $order = $this->createOrder(Order::CONFIRMED);

        $response = $this->post(route('invoice.save'), [
            'order_id' => $order->id,
            'issue_date' => '2026-10-28',
            'due_date' => '2026-11-27',
            'notes' => 'Thanks',
        ]);

        $invoice = Invoice::sole();
        $response->assertRedirect(route('invoice.show', $invoice->id));
        $this->assertSame('0001-00000001', $invoice->fullNumber());
        $this->assertSame('Thanks', $invoice->notes);
        $this->assertSame(Order::COMPLETED, (int) $order->fresh()->status);
    }

    #[Test]
    public function the_due_date_cannot_be_before_the_issue_date(): void
    {
        $order = $this->createOrder(Order::CONFIRMED);

        $this->post(route('invoice.save'), ['order_id' => $order->id, 'issue_date' => '2026-10-28', 'due_date' => '2026-10-27'])
            ->assertSessionHasErrors('due_date');

        $this->assertSame(0, Invoice::count());
    }

    #[Test]
    public function invoicing_a_pending_order_shows_an_error(): void
    {
        $order = $this->createOrder(Order::PENDING);

        $this->post(route('invoice.save'), ['order_id' => $order->id, 'issue_date' => '2026-10-28', 'due_date' => '2026-10-28'])
            ->assertSessionHasErrors('order');

        $this->assertSame(0, Invoice::count());
    }

    #[Test]
    public function an_order_of_another_company_cannot_be_invoiced(): void
    {
        $order = Order::factory()->create(['company_id' => Company::factory()->create()->id, 'status' => Order::CONFIRMED]);

        $this->get(route('invoice.create', $order->order_number))->assertNotFound();
        $this->post(route('invoice.save'), ['order_id' => $order->id, 'issue_date' => '2026-10-28', 'due_date' => '2026-10-28'])
            ->assertSessionHasErrors('order_id');

        $this->assertSame(0, Invoice::count());
    }

    #[Test]
    public function an_invoice_of_another_company_returns_not_found(): void
    {
        $invoice = Invoice::factory()->create();

        $this->get(route('invoice.show', $invoice->id))->assertNotFound();
        $this->post(route('invoice.cancel', $invoice->id))->assertNotFound();
        $this->assertSame(Invoice::ISSUED, $invoice->fresh()->status);
    }

    #[Test]
    public function the_invoice_list_filters_by_status_and_customer(): void
    {
        $issued = $this->createInvoice(['number' => 1]);
        $paid = $this->createInvoice(['number' => 2, 'status' => Invoice::PAID, 'paid_amount' => 121]);
        $otherCustomer = Customer::factory()->create(['company_id' => $this->user->company_id]);
        $ofOtherCustomer = $this->createInvoice(['number' => 3, 'customer_id' => $otherCustomer->id]);
        $foreign = Invoice::factory()->create(['number' => 4]);

        $this->get(route('invoice.index'))
            ->assertOk()
            ->assertSee($issued->fullNumber())
            ->assertSee($paid->fullNumber())
            ->assertDontSee('0001-00000004');

        $this->get(route('invoice.index', ['status' => Invoice::PAID]))
            ->assertSee($paid->fullNumber())
            ->assertDontSee($issued->fullNumber());

        $this->get(route('invoice.index', ['customer_id' => $otherCustomer->id]))
            ->assertSee($ofOtherCustomer->fullNumber())
            ->assertDontSee($issued->fullNumber());

        $this->assertNotSame($foreign->company_id, $this->user->company_id);
    }

    #[Test]
    public function the_invoice_list_filters_by_branch_when_the_company_has_several_branches(): void
    {
        $otherBranch = Branch::factory()->forCompany($this->user->company_id)->create(['code' => '0002', 'name' => 'Norte']);
        $main = $this->createInvoice(['number' => 1]);
        $north = $this->createInvoice(['number' => 1, 'branch_id' => $otherBranch->id, 'series' => '0002']);

        $this->get(route('invoice.index', ['branch_id' => $otherBranch->id]))
            ->assertSee('name="branch_id"', false)
            ->assertSee($north->fullNumber())
            ->assertDontSee($main->fullNumber());
    }

    #[Test]
    public function overdue_invoices_are_flagged_and_filtered(): void
    {
        $overdue = $this->createInvoice(['number' => 1, 'due_date' => today()->subDay()]);
        $current = $this->createInvoice(['number' => 2, 'due_date' => today()]);

        $this->get(route('invoice.index', ['status' => 'overdue']))
            ->assertSee($overdue->fullNumber())
            ->assertSee(__('Overdue'))
            ->assertDontSee($current->fullNumber());
    }

    #[Test]
    public function the_invoice_page_shows_the_equivalent_in_the_company_currency(): void
    {
        $this->user->company->update(['currency' => 'ARS']);
        $invoice = $this->createInvoice([
            'currency' => 'USD',
            'exchange_rate' => 1200,
            'total' => 100,
            'issue_date' => '2026-10-22',
            'issuer_data' => ['company' => ['business_name' => 'Issuer', 'currency' => 'ARS'], 'branch' => ['code' => '0001', 'name' => 'Main']],
        ]);

        $this->get(route('invoice.show', $invoice->id))
            ->assertOk()
            ->assertSee('Equivalent: ARS 120,000.00 (1 USD = 1,200.00 ARS, 22/10/2026)');
    }

    #[Test]
    public function the_invoice_page_shows_no_equivalent_in_the_company_currency(): void
    {
        $invoice = $this->createInvoice();

        $this->get(route('invoice.show', $invoice->id))
            ->assertOk()
            ->assertSee($invoice->fullNumber())
            ->assertDontSee('Equivalent:');
    }

    #[Test]
    public function cancelling_an_invoice_keeps_its_number_and_reopens_the_order(): void
    {
        $order = $this->createOrder(Order::CONFIRMED);
        $invoice = app(InvoiceService::class)->issue($order);

        $this->post(route('invoice.cancel', $invoice->id))
            ->assertRedirect(route('invoice.show', $invoice->id))
            ->assertSessionHas('status', 'success');

        $this->assertSame(Invoice::CANCELLED, $invoice->fresh()->status);
        $this->assertSame(1, $invoice->fresh()->number);
        $this->assertSame(Order::CONFIRMED, (int) $order->fresh()->status);
        $this->assertTrue($order->fresh()->canBeInvoiced());
    }

    #[Test]
    public function an_invoice_with_payments_cannot_be_cancelled(): void
    {
        $invoice = $this->createInvoice(['status' => Invoice::PARTIALLY_PAID, 'paid_amount' => 50]);
        $payment = Transaction::factory()->create(['company_id' => $this->user->company_id, 'type' => Transaction::INCOME, 'status' => Transaction::PAID, 'amount' => 50, 'currency' => 'EUR']);
        $payment->invoice_id = $invoice->id;
        $payment->saveQuietly();

        $this->post(route('invoice.cancel', $invoice->id))
            ->assertSessionHas('message', __('Delete the payments of the invoice before cancelling it'));

        $this->assertSame(Invoice::PARTIALLY_PAID, $invoice->fresh()->status);
    }

    #[Test]
    public function a_user_without_read_invoice_cannot_list_invoices(): void
    {
        $user = User::factory()->create(['company_id' => $this->user->company_id]);

        $this->actingAs($user)->get(route('invoice.index'))->assertForbidden();
    }

    #[Test]
    public function a_seller_can_invoice_but_cannot_cancel(): void
    {
        $this->seed([RoleSeeder::class, PermissionSeeder::class]);
        $seller = User::factory()->create(['company_id' => $this->user->company_id]);
        $seller->assignRole('Seller');
        $invoice = $this->createInvoice();

        $this->actingAs($seller)->get(route('invoice.index'))->assertOk();
        $this->actingAs($seller)->post(route('invoice.cancel', $invoice->id))->assertForbidden();

        $this->assertTrue($seller->can('create invoice'));
        $this->assertTrue($seller->can('create payment'));
        $this->assertFalse($seller->can('delete invoice'));
        $this->assertSame(Invoice::ISSUED, $invoice->fresh()->status);
    }

    #[Test]
    public function accountants_and_company_admins_have_every_invoice_permission(): void
    {
        $this->seed([RoleSeeder::class, PermissionSeeder::class]);

        foreach (['Accountant', 'CompanyAdmin'] as $role) {
            $user = User::factory()->create(['company_id' => $this->user->company_id]);
            $user->assignRole($role);

            foreach (['create invoice', 'read invoice', 'delete invoice', 'create payment'] as $permission) {
                $this->assertTrue($user->can($permission), "$role should have $permission");
            }
        }
    }

    #[Test]
    public function the_invoice_page_shows_a_red_badge_until_it_is_paid_and_a_green_one_once_paid(): void
    {
        $pending = $this->createInvoice(['number' => 1]);
        $partiallyPaid = $this->createInvoice(['number' => 2, 'status' => Invoice::PARTIALLY_PAID, 'paid_amount' => 50]);
        $paid = $this->createInvoice(['number' => 3, 'status' => Invoice::PAID, 'paid_amount' => 121]);

        $this->get(route('invoice.show', $pending->id))->assertSee(__('Pending payment'))->assertSee('#dc3545');
        $this->get(route('invoice.show', $partiallyPaid->id))->assertSee(__('Pending payment'))->assertSee('#dc3545');
        $this->get(route('invoice.show', $paid->id))->assertSee(__('Paid in full'))->assertSee('#198754')->assertDontSee(__('Pending payment'));
    }

    #[Test]
    public function the_payment_badge_is_below_the_order_number(): void
    {
        $order = $this->createOrder(Order::CONFIRMED);
        $invoice = app(InvoiceService::class)->issue($order);

        $this->get(route('invoice.show', $invoice->id))
            ->assertSeeInOrder([$invoice->fullNumber(), '#'.$order->orderNumber(), __('Pending payment')]);
    }

    #[Test]
    public function the_invoice_shows_the_company_logo(): void
    {
        $company = $this->user->company;
        $directory = storage_path('app/public/company/'.Str::slug($company->name, '_'));
        File::ensureDirectoryExists($directory);
        File::put($directory.'/logo-test.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII='));
        $company->update(['logo' => 'logo-test.png']);
        $invoice = $this->createInvoice();

        try {
            $this->get(route('invoice.show', $invoice->id))->assertSee('data:image/png;base64,', false);
        } finally {
            File::delete($directory.'/logo-test.png');
        }
    }

    #[Test]
    public function the_pdf_shows_the_payment_badge(): void
    {
        $paid = $this->createInvoice(['status' => Invoice::PAID, 'paid_amount' => 121]);

        $html = view('invoice.pdf', ['invoice' => $paid->load(['items', 'order', 'company']), 'bankAccounts' => collect()])->render();

        $this->assertStringContainsString(__('Paid in full'), $html);
        $this->assertStringContainsString('#198754', $html);
    }

    #[Test]
    public function every_invoice_translation_key_exists_in_english_and_spanish(): void
    {
        $files = [
            ...File::allFiles(resource_path('views/invoice')),
            ...File::allFiles(app_path('Http/Controllers/Invoice')),
            app_path('Http/Requests/InvoiceSaveRequest.php'),
            app_path('Services/InvoiceService.php'),
            app_path('Models/Invoice.php'),
        ];

        $keys = collect($files)
            ->flatMap(function (SplFileInfo|string $file): array {
                preg_match_all('/__\(\s*([\'"])(.*?)(?<!\\\\)\1\s*[,)]/', File::get((string) $file), $matches);

                return $matches[2];
            })
            ->push('Invoices', 'Issued', 'Partially paid', 'Paid', 'Cancelled', 'Overdue', 'Pending payment', 'Paid in full', ...array_values(Transaction::PAYMENT_METHODS))
            ->unique();

        foreach (['en', 'es'] as $locale) {
            $translations = json_decode(File::get(lang_path($locale.'.json')), true);

            $this->assertSame([], $keys->reject(fn (string $key): bool => array_key_exists($key, $translations))->values()->all(), "Missing {$locale} translations");
        }
    }

    #[Test]
    public function the_invoice_page_renders_in_spanish(): void
    {
        $this->user->update(['lang' => 'es']);
        $invoice = $this->createInvoice(['due_date' => today()->subDay()]);

        $this->get(route('invoice.show', $invoice->id))
            ->assertOk()
            ->assertSee('Factura '.$invoice->fullNumber())
            ->assertSee('Pendiente de pago')
            ->assertSee('Fecha de vencimiento')
            ->assertSee('Anular factura');
    }

    private function createOrder(int $status, string $currency = 'EUR'): Order
    {
        $order = Order::factory()->create([
            'company_id' => $this->user->company_id,
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'currency' => $currency,
            'status' => $status,
        ]);
        Item::factory()->create([
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'product_id' => Product::factory()->create(['company_id' => $this->user->company_id])->id,
            'quantity' => 1,
            'unit_price' => 100,
            'discount' => 0,
            'tax' => 21,
        ]);

        return $order;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createInvoice(array $attributes = []): Invoice
    {
        return Invoice::factory()->create([
            'company_id' => $this->user->company_id,
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            ...$attributes,
        ]);
    }
}
