<?php

declare(strict_types=1);

namespace Database\Factories\Invoice;

use App\Models\Invoice;
use App\Models\Invoice\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'product_id' => null,
            'description' => fake()->words(3, true),
            'sku' => fake()->bothify('SKU-####'),
            'quantity' => 1,
            'unit_price' => 100,
            'discount' => 0,
            'tax' => 21,
            'subtotal' => 100,
            'tax_amount' => 21,
            'total' => 121,
        ];
    }
}
