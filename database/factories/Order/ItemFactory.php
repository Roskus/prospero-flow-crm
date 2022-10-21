<?php

namespace Database\Factories\Order;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'order_id' => Order::all()->random(),
            'product_id' => Product::all()->random(),
            'quantity' => fake()->numberBetween(1, 99),
            'unit_price' => fake()->randomFloat(5, 1, 100000),
        ];
    }
}
