<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Squire\Models\Country;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'business_name' => fake()->name(),
            'vat' => fake()->bothify('??#########'),
            'logo' => null,
            'signature_html' => fake()->randomHtml(),
            'phone' => fake()->numerify('6########'),
            'email' => fake()->email(),
            'country_id' => Country::all()->random(),
            'province' => fake()->word(),
            'city' => fake()->city(),
            'street' => fake()->streetAddress(),
            'zipcode' => fake()->numerify('#####'),
            'currency' => fake()->bothify('???'),
            'website' => 'https://'.fake()->domainName(),
            'status' => Company::ACTIVE,
        ];
    }
}
