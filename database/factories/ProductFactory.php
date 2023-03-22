<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\=Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'name' => $this->faker->sentence(2),
            'model' => $this->faker->sentence(2),
            'sku' => $this->faker->randomNumber(),
            'barcode' => $this->faker->randomNumber(),
            'photo' => $this->faker->filePath(),
            'cost' => $this->faker->randomFloat(2, 0, 10000),
            'price' => $this->faker->randomFloat(2, 0, 10000),
            'currency' => 'EUR',
            'min_stock_quantity' => $this->faker->randomNumber(),
            'quantity' => $this->faker->randomNumber(),
            'description' => $this->faker->sentence(2),
            'elaboration_date' => $this->faker->date(),
            'expiration_date' => $this->faker->date(),
            'tags' => json_encode([fake()->word(), fake()->word()]),
            'status' => 1,
        ];
    }
}
