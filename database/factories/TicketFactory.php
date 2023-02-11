<?php

declare(strict_types=1);

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
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->sentence(),
            'company_id' => Company::factory(),
            'customer_id' => Customer::factory(),
            'created_by' => User::factory(),
            'assigned_to' => User::factory(),
            'priority' => $this->faker->randomElement(['low', 'normal', 'high', 'urgent']),
            'type' => $this->faker->randomElement(['', 'question', 'incident', 'issue']),
            'status' => $this->faker->randomElement(['new', 'assigned', 'duplicated', 'closed']),
            'closed_at' => $this->faker->randomElement([null, $this->faker->dateTime()]),
        ];
    }
}
