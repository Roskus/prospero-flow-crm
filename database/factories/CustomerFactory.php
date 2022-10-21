<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Industry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Squire\Models\Country;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "company_id" => Company::all()->random(),
            "name" => fake()->name(),
            "business_name" => fake()->name(),
            "vat" => fake()->bothify('??#########'),
            "dob" => fake()->date(),
            "phone" => fake()->tollFreePhoneNumber(),
            "mobile" => fake()->tollFreePhoneNumber(),
            "email" => fake()->email(),
            "country_id" => Country::all()->random(),
            "status" => fake()->randomElement(['open', 'recall', 'quote', 'quoted', 'waiting_for_answer', 'standby', 'closed']),
            "province" => fake()->word(),
            "city" => fake()->city(),
            "locality" => fake()->word(),
            "street" => fake()->streetAddress(),
            "zipcode" => fake()->numerify('#####'),
            "schedule_contact" => fake()->dateTimeBetween('-2 weeks', 'now'),
            "industry_id" => Industry::all()->random(),
            "opt_in" => 1,
            "website" => fake()->domainName(),
            "notes" => fake()->text(),
            "seller_id" => User::all()->random(),
            "linkedin" => fake()->url(),
            "facebook" => fake()->url(),
            "instagram" => fake()->url(),
            "twitter" => fake()->url(),
            "youtube" => fake()->url(),
            "tiktok" => fake()->url(),
        ];
    }
}
