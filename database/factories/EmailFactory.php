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
            'company_id' => Company::all()->random()->id,
            'template_id' => EmailTemplate::all()->random()->id,
            'version' => 1,
            'from' => $this->faker->email(),
            'to' => $this->faker->email(),
            'cc' => $this->faker->email(),
            'subject' => $this->faker->title(),
            'body' => $this->faker->text(),
            'lang' => $this->faker->randomElement(array_keys(config('app.locales'))),
            'status' => $this->faker->randomElement(['draft','queue','sent','error']),
            'schedule_send' => now(),
        ];
    }
}
