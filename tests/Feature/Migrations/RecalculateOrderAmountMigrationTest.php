<?php

declare(strict_types=1);

namespace Tests\Feature\Migrations;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Order\Item;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RecalculateOrderAmountMigrationTest extends TestCase
{
    #[Test]
    public function orders_with_items_get_the_total_of_their_own_items(): void
    {
        $ownOrder = $this->order($this->user->company, 1715);
        $this->item($ownOrder, 3, 10, 10);
        $this->item($ownOrder, 1, 5, 0);
        $otherOrder = $this->order(Company::factory()->create(), 500);
        $this->item($otherOrder, 50, 10, 0);

        $this->assertSame($ownOrder->order_number, $otherOrder->order_number);

        $this->runMigration();

        $this->assertEquals(32.0, $ownOrder->fresh()->amount);
        $this->assertEquals(500.0, $otherOrder->fresh()->amount);
    }

    #[Test]
    public function the_migration_keeps_the_precision_of_the_items_and_refresh_amount_rounds_each_line(): void
    {
        $order = $this->order($this->user->company, 0);
        $this->item($order, 1, 1.004, 0);

        $this->runMigration();
        $migrated = (float) $order->fresh()->amount;
        $order->fresh()->refreshAmount();

        $this->assertEqualsWithDelta(1.004, $migrated, 0.000001);
        $this->assertEqualsWithDelta(1.0, (float) $order->fresh()->amount, 0.000001);
    }

    #[Test]
    public function orders_without_items_keep_their_amount(): void
    {
        $order = $this->order($this->user->company, 99.5);

        $this->runMigration();

        $this->assertEquals(99.5, $order->fresh()->amount);
    }

    private function order(Company $company, float $amount): Order
    {
        $order = Order::factory()->create([
            'company_id' => $company->id,
            'customer_id' => Customer::factory()->create(['company_id' => $company->id])->id,
            'currency' => $company->currency,
        ]);
        DB::table('order')->where('id', $order->id)->update(['amount' => $amount]);

        return $order;
    }

    private function item(Order $order, int $quantity, float $unitPrice, float $discount): void
    {
        Item::factory()->create([
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'product_id' => Product::factory()->create(['company_id' => $order->company_id])->id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount' => $discount,
            'tax' => 21,
        ]);
    }

    private function runMigration(): void
    {
        $migration = require database_path('migrations/2026_10_27_172832_recalculate_order_amount_from_own_items.php');
        $migration->up();
    }
}
