<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Order\Item;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderAnnualSalesTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;

    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(Carbon::parse('2026-10-15 12:00'));
        $this->company = Company::factory()->create(['currency' => 'EUR']);
        $this->customer = Customer::factory()->create(['company_id' => $this->company->id, 'name' => 'Ana', 'business_name' => 'García']);
    }

    #[Test]
    public function each_month_is_compared_with_the_previous_year(): void
    {
        $this->sale('2025-03-10', 1000);
        $this->sale('2026-03-05', 900);
        $this->sale('2026-03-20', 600);

        $march = (new Order)->getAnnualSalesReport($this->company->id, 2026)['months'][3];

        $this->assertSame(1500.0, $march['total']);
        $this->assertSame(2, $march['orders']);
        $this->assertSame(1000.0, $march['previous_total']);
        $this->assertSame(50.0, $march['change']);
    }

    #[Test]
    public function every_month_is_present_and_without_previous_sales_there_is_no_change(): void
    {
        $this->sale('2026-02-01', 100);

        $months = (new Order)->getAnnualSalesReport($this->company->id, 2026)['months'];

        $this->assertCount(12, $months);
        $this->assertSame(0.0, $months[12]['total']);
        $this->assertNull($months[2]['change']);
    }

    #[Test]
    public function pending_and_canceled_orders_are_not_sales(): void
    {
        $this->sale('2026-05-01', 100, Order::PENDING);
        $this->sale('2026-05-02', 100, Order::CANCELED);
        $this->sale('2026-05-03', 100, Order::COMPLETED);

        $this->assertSame(100.0, (new Order)->getAnnualSalesReport($this->company->id, 2026)['months'][5]['total']);
    }

    #[Test]
    public function foreign_currency_orders_use_their_rate(): void
    {
        $this->company->update(['currency' => 'ARS']);
        $this->company->currencyRates()->create(['currency' => 'USD', 'conversion_rate' => 1200]);
        $this->sale('2026-06-01', 100, currency: 'USD');

        $this->assertSame(120000.0, (new Order)->getAnnualSalesReport($this->company->id, 2026)['months'][6]['total']);
    }

    #[Test]
    public function the_current_year_is_compared_up_to_the_same_day(): void
    {
        $this->sale('2025-10-15', 300);
        $this->sale('2025-10-16', 700);
        $this->sale('2026-01-10', 500);

        $report = (new Order)->getAnnualSalesReport($this->company->id, 2026);

        $this->assertSame(500.0, $report['total']);
        $this->assertSame(300.0, $report['previous_total']);
        $this->assertSame('2025-10-15', $report['previous_until']->toDateString());
        $this->assertSame(66.7, $report['change']);
    }

    #[Test]
    public function a_past_year_is_compared_with_the_whole_previous_year(): void
    {
        $this->sale('2024-12-31', 400);
        $this->sale('2025-06-01', 800);

        $report = (new Order)->getAnnualSalesReport($this->company->id, 2025);

        $this->assertSame(400.0, $report['previous_total']);
        $this->assertSame(100.0, $report['change']);
    }

    #[Test]
    public function sales_of_other_companies_are_not_included(): void
    {
        $other = Company::factory()->create();
        Order::factory()->create(['company_id' => $other->id, 'customer_id' => Customer::factory()->create(['company_id' => $other->id])->id, 'status' => Order::CONFIRMED, 'order_date' => '2026-04-01', 'amount' => 999]);

        $report = (new Order)->getAnnualSalesReport($this->company->id, 2026);

        $this->assertSame(0.0, $report['months'][4]['total']);
        $this->assertSame([], $report['top_customers']);
    }

    #[Test]
    public function the_rankings_keep_deleted_products_and_order_by_amount(): void
    {
        $coffee = Product::factory()->create(['company_id' => $this->company->id, 'name' => 'Coffee']);
        $tea = Product::factory()->create(['company_id' => $this->company->id, 'name' => 'Tea']);
        $this->sale('2026-02-01', 0, lines: [[$coffee, 3, 10], [$tea, 1, 100]]);
        $this->sale('2026-02-02', 0, lines: [[$coffee, 2, 10]]);
        $coffee->delete();

        $report = (new Order)->getAnnualSalesReport($this->company->id, 2026);

        $this->assertSame([['name' => 'Tea', 'quantity' => 1, 'total' => 100.0], ['name' => 'Coffee', 'quantity' => 5, 'total' => 50.0]], $report['top_products']);
        $this->assertSame([['name' => 'Ana García', 'orders' => 2, 'total' => 150.0]], $report['top_customers']);
    }

    #[Test]
    public function the_seller_filter_limits_every_figure(): void
    {
        $seller = User::factory()->create(['company_id' => $this->company->id, 'first_name' => 'Sara', 'last_name' => 'Vende']);
        $this->sale('2026-07-01', 200, seller: $seller);
        $this->sale('2026-07-02', 300);

        $report = (new Order)->getAnnualSalesReport($this->company->id, 2026, $seller->id);

        $this->assertSame(200.0, $report['months'][7]['total']);
        $this->assertSame(200.0, $report['total']);
        $this->assertSame([['name' => 'Sara Vende', 'orders' => 1, 'total' => 200.0]], $report['sellers']);
    }

    /**
     * @param  list<array{0: Product, 1: int, 2: float}>  $lines
     */
    private function sale(string $date, float $amount, int $status = Order::CONFIRMED, string $currency = 'EUR', ?User $seller = null, array $lines = []): Order
    {
        $order = Order::factory()->create([
            'company_id' => $this->company->id,
            'customer_id' => $this->customer->id,
            'seller_id' => ($seller ?? User::factory()->create(['company_id' => $this->company->id]))->id,
            'status' => $status,
            'currency' => $currency,
            'order_date' => $date,
            'amount' => $amount,
        ]);

        foreach ($lines as [$product, $quantity, $price]) {
            Item::factory()->create(['order_id' => $order->id, 'order_number' => $order->order_number, 'product_id' => $product->id, 'quantity' => $quantity, 'unit_price' => $price, 'discount' => 0, 'tax' => 0]);
        }

        if ($lines !== []) {
            $order->refreshAmount();
        }

        return $order;
    }
}
