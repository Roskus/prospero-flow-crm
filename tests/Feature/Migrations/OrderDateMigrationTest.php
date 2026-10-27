<?php

declare(strict_types=1);

namespace Tests\Feature\Migrations;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderDateMigrationTest extends TestCase
{
    #[Test]
    public function existing_orders_take_their_creation_date_as_order_date(): void
    {
        $migration = require database_path('migrations/2026_10_27_160622_add_order_date_to_order_table.php');
        $migration->down();

        $orderId = DB::table('order')->insertGetId([
            'company_id' => $this->user->company_id,
            'branch_id' => Branch::ofCompany($this->user->company_id)->where('is_main', true)->value('id'),
            'customer_id' => Customer::factory()->create(['company_id' => $this->user->company_id])->id,
            'status' => Order::PENDING,
            'amount' => 0,
            'created_at' => '2025-02-14 18:45:00',
            'updated_at' => '2025-02-14 18:45:00',
        ]);

        $migration->up();

        $this->assertSame('2025-02-14', DB::table('order')->where('id', $orderId)->value('order_date'));
    }

    #[Test]
    public function orders_inserted_without_order_date_default_to_today(): void
    {
        $orderId = DB::table('order')->insertGetId([
            'company_id' => $this->user->company_id,
            'branch_id' => Branch::ofCompany($this->user->company_id)->where('is_main', true)->value('id'),
            'customer_id' => Customer::factory()->create(['company_id' => $this->user->company_id])->id,
            'status' => Order::PENDING,
            'amount' => 0,
        ]);

        $this->assertSame(now()->toDateString(), DB::table('order')->where('id', $orderId)->value('order_date'));
    }
}
