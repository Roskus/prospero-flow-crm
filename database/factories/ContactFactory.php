<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;
use Squire\Models\Country;

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
            'lead_id' => Lead::all()->random(),
            'customer_id' => Customer::all()->random(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'phone' => fake()->tollFreePhoneNumber(),
            'email' => fake()->email(),
            'linkedin' => fake()->url(),
            'country' => Country::all()->random(),
            'notes' => fake()->text(),
        ];
    }
}
