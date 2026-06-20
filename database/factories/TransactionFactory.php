<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'issue_date' => $this->faker->date('Y-m-d'),
            'due_date' => $this->faker->date('Y-m-d'),
            'name' => $this->faker->words(3, asText: true),
            'type' => $this->faker->randomElement(['income', 'expense']),
            'amount' => $this->faker->randomFloat(2, 10, 10000),
            'status' => $this->faker->randomElement(['pending', 'paid', 'overdue']),
            'notes' => $this->faker->sentence(),
        ];
    }
}
