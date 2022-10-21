<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'company_id' => Company::all()->random(),
            'customer_id' => Customer::all()->random(),
            'amount' => fake()->randomFloat(5, 1, 100000),
            'status' => fake()->randomElement([0, 1, 2, 3]),
        ];
    }
}
