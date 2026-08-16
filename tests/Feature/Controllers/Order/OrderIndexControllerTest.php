<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Order;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderIndexControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_the_order_index_when_an_order_has_a_null_order_number(): void
    {
        $this->createOrderWithoutNumber();

        $response = $this->get('/order');

        $response->assertOk();
        $response->assertSee('Orders');
    }

    #[Test]
    public function it_renders_action_buttons_for_orders_with_an_order_number(): void
    {
        $order = Order::factory()->create([
            'company_id' => $this->user->company_id,
            'status' => Order::PENDING,
        ]);

        $response = $this->get('/order');

        $response->assertOk();
        $response->assertSee(route('order.confirm', $order->order_number));
    }

    #[Test]
    public function it_does_not_render_a_confirm_form_for_orders_without_an_order_number(): void
    {
        $this->createOrderWithoutNumber();

        $response = $this->get('/order');

        $response->assertOk();
        $response->assertDontSee('order/confirm/');
    }

    #[Test]
    public function the_backfill_migration_assigns_sequential_order_numbers_per_company(): void
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();

        $existingA = $this->insertOrderRaw($companyA->id, ['order_number' => 5]);
        $nullA1 = $this->insertOrderRaw($companyA->id, ['order_number' => null]);
        $nullA2 = $this->insertOrderRaw($companyA->id, ['order_number' => null]);
        $nullB = $this->insertOrderRaw($companyB->id, ['order_number' => null]);

        $this->runBackfillMigration();

        $this->assertSame(5, (int) DB::table('order')->where('id', $existingA)->value('order_number'));
        $this->assertSame(6, (int) DB::table('order')->where('id', $nullA1)->value('order_number'));
        $this->assertSame(7, (int) DB::table('order')->where('id', $nullA2)->value('order_number'));
        $this->assertSame(1, (int) DB::table('order')->where('id', $nullB)->value('order_number'));
    }

    #[Test]
    public function the_backfill_migration_syncs_order_items_and_company_counter(): void
    {
        $company = Company::factory()->create();
        $product = Product::factory()->create();

        $orderId = $this->insertOrderRaw($company->id, ['order_number' => null]);

        DB::table('order_item')->insert([
            'order_id' => $orderId,
            'order_number' => null,
            'product_id' => $product->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->runBackfillMigration();

        $orderNumber = (int) DB::table('order')->where('id', $orderId)->value('order_number');

        $this->assertSame($orderNumber, (int) DB::table('order_item')->where('order_id', $orderId)->value('order_number'));
        $this->assertSame($orderNumber, (int) DB::table('company')->where('id', $company->id)->value('last_order_number'));
    }

    #[Test]
    public function the_order_observer_assigns_a_sequential_order_number_on_create(): void
    {
        $company = Company::factory()->create();
        $customer = Customer::factory()->create(['company_id' => $company->id]);

        $order = Order::create([
            'company_id' => $company->id,
            'customer_id' => $customer->id,
            'status' => Order::PENDING,
        ]);

        $this->assertSame(1, (int) $order->order_number);
        $this->assertSame(1, (int) $company->fresh()->last_order_number);
    }

    #[Test]
    public function the_order_observer_assigns_a_number_when_the_company_is_soft_deleted(): void
    {
        $company = Company::factory()->create();
        $company->delete();

        $customer = Customer::factory()->create(['company_id' => $company->id]);

        $order = Order::create([
            'company_id' => $company->id,
            'customer_id' => $customer->id,
            'status' => Order::PENDING,
        ]);

        $this->assertSame(1, (int) $order->order_number);
        $this->assertSame(1, (int) Company::withTrashed()->where('id', $company->id)->value('last_order_number'));
    }

    private function createOrderWithoutNumber(): Order
    {
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id]);

        return Order::withoutEvents(function () use ($customer) {
            $order = new Order;
            $order->company_id = $this->user->company_id;
            $order->customer_id = $customer->id;
            $order->status = Order::PENDING;
            $order->save();

            return $order;
        });
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function insertOrderRaw(int $companyId, array $overrides = []): int
    {
        $customer = Customer::factory()->create(['company_id' => $companyId]);

        return DB::table('order')->insertGetId(array_merge([
            'company_id' => $companyId,
            'customer_id' => $customer->id,
            'status' => Order::PENDING,
            'amount' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ], $overrides));
    }

    private function runBackfillMigration(): void
    {
        $migration = require database_path('migrations/2026_08_16_173916_backfill_order_number_for_null_orders.php');
        $migration->up();
    }
}
