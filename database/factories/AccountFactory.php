<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
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
            'issue_date' => fake()->dateTimeBetween('-2 weeks', 'now'),
            'name' => fake()->name(),
            'amount' => fake()->randomFloat(5, 1, 100000),
            'status' => Account::ACTIVE,
        ];
    }
}
