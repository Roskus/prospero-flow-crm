<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Order;

use App\Models\Order\Item;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ItemTest extends TestCase
{
    #[Test]
    public function it_can_access_unit_price_from_database(): void
    {
        $item = Item::factory()->create([
            'unit_price' => 100.00,
            'quantity' => 5,
            'discount' => 10,
            'tax' => 21,
        ]);

        $this->assertSame(100.00, $item->unit_price);
    }

    #[Test]
    public function it_calculates_subtotal_without_discount(): void
    {
        $item = Item::factory()->create([
            'unit_price' => 100.00,
            'quantity' => 3,
            'discount' => 0,
            'tax' => 21,
        ]);

        $this->assertSame(300.0, $item->getSubTotal());
    }

    #[Test]
    public function it_calculates_subtotal_with_discount(): void
    {
        $item = Item::factory()->create([
            'unit_price' => 100.00,
            'quantity' => 3,
            'discount' => 10,
            'tax' => 21,
        ]);

        $this->assertSame(270.0, $item->getSubTotal());
    }

    #[Test]
    public function it_calculates_tax_amount(): void
    {
        $item = Item::factory()->create([
            'unit_price' => 100.00,
            'quantity' => 3,
            'discount' => 10,
            'tax' => 21,
        ]);

        $this->assertEqualsWithDelta(56.7, $item->getTaxAmount(), 0.00001);
    }
}
