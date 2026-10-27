<?php

declare(strict_types=1);

namespace Tests\Feature\Migrations;

use App\Models\Company;
use App\Models\Order;
use App\Models\Product;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CurrencyBackfillMigrationTest extends TestCase
{
    #[Test]
    public function products_and_orders_left_at_eur_get_the_currency_of_their_company(): void
    {
        $argentina = Company::factory()->create(['currency' => 'ARS']);
        $spain = Company::factory()->create(['currency' => 'EUR']);
        $argentina->currencyRates()->create(['currency' => 'EUR', 'conversion_rate' => 1300]);
        $defaultProduct = Product::factory()->create(['company_id' => $argentina->id, 'currency' => 'EUR']);
        $dollarProduct = Product::factory()->create(['company_id' => $argentina->id, 'currency' => 'USD']);
        $spanishProduct = Product::factory()->create(['company_id' => $spain->id, 'currency' => 'EUR']);
        $defaultOrder = Order::factory()->create(['company_id' => $argentina->id, 'currency' => 'EUR']);
        $spanishOrder = Order::factory()->create(['company_id' => $spain->id, 'currency' => 'EUR']);

        $this->runMigration();
        $this->runMigration();

        $this->assertSame('ARS', $defaultProduct->fresh()->currency);
        $this->assertSame('USD', $dollarProduct->fresh()->currency);
        $this->assertSame('EUR', $spanishProduct->fresh()->currency);
        $this->assertSame('ARS', $defaultOrder->fresh()->currency);
        $this->assertSame('EUR', $spanishOrder->fresh()->currency);
    }

    private function runMigration(): void
    {
        (require database_path('migrations/2026_10_22_215045_backfill_product_and_order_currency.php'))->up();
    }
}
