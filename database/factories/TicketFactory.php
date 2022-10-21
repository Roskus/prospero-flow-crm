<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => fake()->text(50),
            'description' => fake()->text(),
            'company_id' => Company::all()->random(),
            'customer_id' => Customer::all()->random(),
            'created_by' => User::all()->random(),
            'assigned_to' => User::all()->random(),
            'priority' => fake()->randomElement(['low', 'normal', 'high', 'urgent']),
            'type' => fake()->randomElement(['', 'question', 'incident', 'issue']),
            'status' => fake()->randomElement(['new', 'assigned', 'duplicated', 'closed']),
            'closed_at' => fake()->randomElement([null, fake()->dateTimeThisMonth()]),
        ];
    }
}
