<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Company;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Squire\Models\Country;

/**
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
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
            'name' => fake()->word(),
            'business_name' => fake()->word(),
            'vat' => fake()->bothify('??#########'),
            'phone' => fake()->numerify('6########'),
            'email' => fake()->email(),
            'website' => 'https://'.fake()->domainName(),
            'country_id' => Country::all()->random(),
            'province' => fake()->word(),
            'city' => fake()->city(),
            'street' => fake()->streetAddress(),
            'zipcode' => fake()->numerify('#####'),
        ];
    }
}
