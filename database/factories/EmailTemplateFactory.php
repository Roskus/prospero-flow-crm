<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmailTemplate>
 */
class EmailTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'from' => fake()->email(),
            'subject' => fake()->text(50),
            'body' => fake()->text(),
            'lang' => fake()->randomElement(array_keys(config('app.locales'))),
            'version' => fake()->randomFloat(2, 1, 9),
        ];
    }
}
