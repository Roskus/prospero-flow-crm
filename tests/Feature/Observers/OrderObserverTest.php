<?php

declare(strict_types=1);

namespace Tests\Feature\Observers;

use App\Models\Company;
use App\Models\Order;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderObserverTest extends TestCase
{
    #[Test]
    public function the_first_order_of_a_company_gets_number_one(): void
    {
        $company = Company::factory()->create(['last_order_number' => 0]);

        $order = Order::factory()->create(['company_id' => $company->id]);

        $this->assertSame(1, (int) $order->fresh()->order_number);
        $this->assertSame(1, (int) $company->fresh()->last_order_number);
    }

    #[Test]
    public function next_orders_of_the_same_company_increment_by_one(): void
    {
        $company = Company::factory()->create(['last_order_number' => 0]);

        $numbers = Order::factory()->count(3)->create(['company_id' => $company->id])
            ->map(fn (Order $order): int => (int) $order->fresh()->order_number)
            ->all();

        $this->assertSame([1, 2, 3], $numbers);
        $this->assertSame(3, (int) $company->fresh()->last_order_number);
    }

    #[Test]
    public function each_company_has_its_own_sequence(): void
    {
        $companyA = Company::factory()->create(['last_order_number' => 0]);
        $companyB = Company::factory()->create(['last_order_number' => 0]);

        $firstA = Order::factory()->create(['company_id' => $companyA->id]);
        $firstB = Order::factory()->create(['company_id' => $companyB->id]);
        $secondA = Order::factory()->create(['company_id' => $companyA->id]);

        $this->assertSame(1, (int) $firstA->fresh()->order_number);
        $this->assertSame(1, (int) $firstB->fresh()->order_number);
        $this->assertSame(2, (int) $secondA->fresh()->order_number);
        $this->assertSame(1, (int) $companyB->fresh()->last_order_number);
    }

    #[Test]
    public function the_number_sent_on_creation_is_replaced_by_the_company_sequence(): void
    {
        $company = Company::factory()->create(['last_order_number' => 7]);

        $order = Order::factory()->create(['company_id' => $company->id, 'order_number' => 500]);

        $this->assertSame(8, (int) $order->fresh()->order_number);
    }

    #[Test]
    public function updating_an_order_keeps_its_number_and_the_company_counter(): void
    {
        $company = Company::factory()->create(['last_order_number' => 0]);
        $order = Order::factory()->create(['company_id' => $company->id]);

        $order->update(['amount' => 99.5, 'status' => Order::CONFIRMED]);

        $this->assertSame(1, (int) $order->fresh()->order_number);
        $this->assertSame(1, (int) $company->fresh()->last_order_number);
    }

    #[Test]
    public function after_the_backfill_the_next_order_continues_from_the_historical_max(): void
    {
        $company = Company::factory()->create(['last_order_number' => 0]);
        $order = Order::factory()->create(['company_id' => $company->id]);
        $order->forceFill(['order_number' => 41])->saveQuietly();
        $deleted = Order::factory()->create(['company_id' => $company->id]);
        $deleted->forceFill(['order_number' => 90])->saveQuietly();
        $deleted->delete();
        $company->forceFill(['last_order_number' => 0])->saveQuietly();

        $migration = require database_path('migrations/2026_05_15_224602_backfill_company_last_order_number_from_orders.php');
        $migration->up();

        $this->assertSame(41, (int) $company->fresh()->last_order_number);
        $this->assertSame(42, (int) Order::factory()->create(['company_id' => $company->id])->fresh()->order_number);
    }
}
