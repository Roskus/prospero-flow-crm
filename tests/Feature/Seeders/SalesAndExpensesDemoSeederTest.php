<?php

declare(strict_types=1);

namespace Tests\Feature\Seeders;

use App\Models\Company;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use Database\Seeders\SalesAndExpensesDemoSeeder;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SalesAndExpensesDemoSeederTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(Carbon::parse('2026-10-18 12:00:00'));
    }

    #[Test]
    public function it_seeds_at_least_fifty_operations_in_the_last_three_months(): void
    {
        $companyId = (int) $this->user->company_id;

        (new SalesAndExpensesDemoSeeder)->run($companyId);

        $orders = Order::withoutGlobalScope('orderCreated')->where('company_id', $companyId)->get();
        $transactions = Transaction::where('company_id', $companyId)->get();

        $this->assertGreaterThanOrEqual(50, $orders->count() + $transactions->count());
        $this->assertCount(45, $orders);
        $this->assertTrue($orders->every(fn (Order $order) => $order->created_at->between('2026-07-18 00:00:00', '2026-10-18 12:00:00')));
        $this->assertTrue($transactions->every(fn (Transaction $transaction) => $transaction->issue_date->between('2026-07-18', '2026-10-18')));
        $this->assertTrue($transactions->contains('type', Transaction::INCOME));
        $this->assertTrue($transactions->contains('type', Transaction::EXPENSE));
    }

    #[Test]
    public function order_amount_matches_its_items(): void
    {
        (new SalesAndExpensesDemoSeeder)->run((int) $this->user->company_id);

        Order::withoutGlobalScope('orderCreated')
            ->where('company_id', $this->user->company_id)
            ->get()
            ->each(function (Order $order): void {
                $this->assertNotEmpty($order->items);
                $this->assertEqualsWithDelta($order->getTotal(), (float) $order->amount, 0.01);
            });
    }

    #[Test]
    public function the_invoice_income_is_the_order_amount_with_taxes_included(): void
    {
        fake()->seed(1);
        mt_srand(1);
        $companyId = (int) $this->user->company_id;

        (new SalesAndExpensesDemoSeeder)->run($companyId);

        $invoicedOrders = Order::withoutGlobalScope('orderCreated')
            ->with('items')
            ->where('company_id', $companyId)
            ->whereIn('status', [Order::CONFIRMED, Order::COMPLETED])
            ->get();

        $this->assertTrue($invoicedOrders->contains(fn (Order $order) => $order->items->contains(fn ($item) => (float) $item->discount > 0 && (float) $item->tax > 0)));
        $invoicedOrders->each(function (Order $order) use ($companyId): void {
            $income = Transaction::where('company_id', $companyId)->where('reference', 'INV-'.$order->order_number)->sole();
            $this->assertSame((string) $order->amount, (string) $income->amount);
        });
    }

    #[Test]
    public function only_confirmed_and_completed_orders_are_invoiced(): void
    {
        $companyId = (int) $this->user->company_id;

        (new SalesAndExpensesDemoSeeder)->run($companyId);

        $invoicedOrders = Order::withoutGlobalScope('orderCreated')
            ->where('company_id', $companyId)
            ->whereIn('status', [Order::CONFIRMED, Order::COMPLETED])
            ->count();

        $this->assertSame($invoicedOrders, Transaction::where('company_id', $companyId)->where('type', Transaction::INCOME)->count());
        $this->assertSame(0, Transaction::where('company_id', $companyId)->where('type', Transaction::INCOME)->where('status', Transaction::PAID)->whereNull('payment_date')->count());
    }

    #[Test]
    public function it_creates_fixed_expenses_for_every_month_in_the_period(): void
    {
        $companyId = (int) $this->user->company_id;

        (new SalesAndExpensesDemoSeeder)->run($companyId);

        $rentMonths = Transaction::where('company_id', $companyId)
            ->where('type', Transaction::EXPENSE)
            ->where('name', 'like', 'Office rent%')
            ->get()
            ->map(fn (Transaction $transaction) => $transaction->issue_date->format('Y-m'))
            ->sort()
            ->values()
            ->all();

        $this->assertSame(['2026-08', '2026-09', '2026-10'], $rentMonths);
    }

    #[Test]
    public function it_keeps_amounts_within_the_transaction_amount_column_with_expensive_products(): void
    {
        $companyId = (int) $this->user->company_id;
        Product::factory()->count(5)->create(['company_id' => $companyId, 'photo' => null, 'price' => 9999.99]);

        (new SalesAndExpensesDemoSeeder)->run($companyId);

        $this->assertLessThan(99999.99, (float) Transaction::where('company_id', $companyId)->max('amount'));
        $this->assertSame(45, Order::withoutGlobalScope('orderCreated')->where('company_id', $companyId)->count());
    }

    #[Test]
    public function it_does_not_touch_other_companies(): void
    {
        $otherCompany = Company::factory()->create();

        (new SalesAndExpensesDemoSeeder)->run((int) $this->user->company_id);

        $this->assertSame(0, Order::withoutGlobalScope('orderCreated')->where('company_id', $otherCompany->id)->count());
        $this->assertSame(0, Transaction::where('company_id', $otherCompany->id)->count());
    }

    #[Test]
    public function seeded_sales_show_up_in_the_sales_report(): void
    {
        (new SalesAndExpensesDemoSeeder)->run((int) $this->user->company_id);

        $response = $this->get('/report/sale?from=2026-07-18&to=2026-10-18');

        $response->assertOk();
        $response->assertViewHas('report', fn (array $report) => $report['current']['total'] > 0 && $report['current']['orders'] > 0);
    }

    #[Test]
    public function it_can_be_run_from_the_command_line_for_the_default_company(): void
    {
        $defaultCompany = Company::find(Company::DEFAULT_COMPANY) ?? Company::factory()->create(['id' => Company::DEFAULT_COMPANY]);
        $this->user->update(['company_id' => $defaultCompany->id]);

        $this->artisan('db:seed', ['--class' => SalesAndExpensesDemoSeeder::class, '--no-interaction' => true])
            ->assertSuccessful();

        $this->assertSame(45, Order::withoutGlobalScope('orderCreated')->where('company_id', $defaultCompany->id)->count());
    }
}
