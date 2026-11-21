<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Carrier;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Carrier>
 */
class CarrierFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => fake()->unique()->company(),
            'country_id' => 'es',
            'phone' => fake()->numerify('1###'),
            'website' => fake()->url(),
        ];
    }

    public function forCompany(Company|int $company): static
    {
        return $this->state(['company_id' => $company instanceof Company ? $company->id : $company]);
    }
}
