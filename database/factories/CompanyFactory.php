<?php

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
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'logo' => null,
            'phone' => fake()->phoneNumber(),
            'email' => fake()->email(),
            'country_id' => Country::all()->random(),
            'website' => 'https://'.fake()->domainName(),
            'status' => Company::ACTIVE,
        ];
    }
}
