<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\EmailTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Email>
 */
class EmailFactory extends Factory
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
            "template_id" => EmailTemplate::all()->random(),
            "version" => fake()->randomFloat(2, 1, 9),
            "from" => fake()->email(),
            "to" => fake()->email(),
            "cc" => fake()->email(),
            "subject" => fake()->text(50),
            "body" => fake()->text(),
            "lang" => fake()->randomElement(array_keys(config('app.locales'))),
            "status" => fake()->randomElement(['draft', 'queue', 'sent', 'error']),
            "schedule_send" => fake()->dateTimeThisMonth(),
        ];
    }
}
