<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Squire\Models\Country;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bank>
 */
class BankFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'uuid' => fake()->uuid(),
            'name' => fake()->words(3, true),
            'country_id' => Country::all()->random(),
            'bic' => Str::upper(fake()->lexify('???????????')),// Max 11
            'phone' => fake()->numerify('6########'),
            'email' => fake()->email(),
            'website' => 'https://'.fake()->domainName(),
        ];
    }
}
