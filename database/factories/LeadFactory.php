<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Company;
use App\Models\Industry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Squire\Models\Country;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
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
            'name' => fake()->name(),
            'business_name' => fake()->name(),
            'dob' => fake()->date(),
            'vat' => fake()->bothify('??#########'),
            'phone' => fake()->tollFreePhoneNumber(),
            'phone2' => fake()->tollFreePhoneNumber(),
            'mobile' => fake()->tollFreePhoneNumber(),
            'email' => fake()->email(),
            'email2' => fake()->email(),
            'website' => 'https://'.fake()->domainName(),
            'linkedin' => fake()->url(),
            'facebook' => fake()->url(),
            'instagram' => fake()->url(),
            'twitter' => fake()->url(),
            'youtube' => fake()->url(),
            'tiktok' => fake()->url(),
            'notes' => fake()->text(),
            'seller_id' => User::factory(),
            'country_id' => Country::all()->random(),
            'province' => fake()->word(),
            'city' => fake()->city(),
            'locality' => fake()->word(),
            'street' => fake()->streetAddress(),
            'zipcode' => fake()->numerify('#####'),
            'schedule_contact' => fake()->dateTimeBetween('-2 weeks', 'now')->format('Y-m-d h:m:s'),
            'industry_id' => Industry::factory(),
            'latitude' => fake()->randomFloat(6, 40.3, 40.5),
            'longitude' => fake()->randomFloat(6, -3.5, -3.9),
            'opt_in' => 1,
            'tags' => [fake()->word(), fake()->word()],
            'status' => fake()->randomElement(['open', 'first_contact', 'recall', 'quote', 'quoted', 'waiting_for_answer', 'standby', 'closed']),
        ];
    }
}
