<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Company;
use App\Models\Order;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderSalesFiguresTest extends TestCase
{
    #[Test]
    public function sales_of_orders_in_several_currencies_are_added_in_the_company_currency(): void
    {
        $company = Company::factory()->create(['currency' => 'ARS']);
        $company->currencyRates()->create(['currency' => 'USD', 'conversion_rate' => 1200]);
        Order::factory()->create(['company_id' => $company->id, 'status' => Order::CONFIRMED, 'amount' => 100000]);
        Order::factory()->create(['company_id' => $company->id, 'status' => Order::COMPLETED, 'amount' => 100, 'currency' => 'USD']);
        Order::factory()->create(['company_id' => $company->id, 'status' => Order::PENDING, 'amount' => 50, 'currency' => 'USD']);

        $order = new Order;
        $kpis = $order->getSalesKpis($company->id, Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth());

        $this->assertEquals(220000, $kpis['total']);
        $this->assertEquals(110000, $kpis['average_ticket']);
        $this->assertEquals(220000, $order->getSalesSummary($company->id)['current_month_total']);
        $this->assertEquals(220000, $order->getSalesReport($company->id, Carbon::now()->startOfMonth(), Carbon::now())['current']['total']);
    }
}
