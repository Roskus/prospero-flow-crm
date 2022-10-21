<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "company_id" => Company::all()->random(),
            "category_id" => Category::all()->random(),
            "brand_id" => Brand::all()->random(),
            "name" => fake()->name(),
            "model" => fake()->word(),
            "sku" => fake()->randomNumber(),
            "barcode" => fake()->ean13(),
            "photo" => null,
            "cost" => fake()->randomFloat(5, 1, 100000),
            "price" => fake()->randomFloat(5, 1, 100000),
            "min_stock_quantity" => fake()->randomNumber(2),
            "quantity" => fake()->randomNumber(2),
            "description" => fake()->text(),
            "elaboration_date" => fake()->date(),
            "expiration_date" => fake()->date(),
            "status" => 1,
        ];
    }
}
