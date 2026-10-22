<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Branch>
 */
class BranchFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => fake()->city(),
            'code' => fake()->unique()->bothify('B###'),
            'is_main' => false,
            'country_id' => 'es',
            'city' => fake()->city(),
            'street' => fake()->streetName(),
            'zipcode' => fake()->numerify('#####'),
            'phone' => fake()->numerify('6########'),
            'email' => fake()->safeEmail(),
        ];
    }

    public function forCompany(Company|int $company): static
    {
        return $this->state(['company_id' => $company instanceof Company ? $company->id : $company]);
    }

    /**
     * A second main branch breaks the one main branch rule; use BranchService::makeMain() in tests about it.
     */
    public function main(): static
    {
        return $this->state(['is_main' => true]);
    }
}
