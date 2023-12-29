<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'company_id' => Company::factory(),
            'lead_id' => null,
            'customer_id' => null,
            'first_name' => fake()->name(),
            'last_name' => fake()->lastName(),
            'mobile' => fake()->numerify('6########'),
            'phone' => fake()->numerify('6########'),
            'email' => fake()->email(),
            'created_at' => now(),
        ];
    }
}
