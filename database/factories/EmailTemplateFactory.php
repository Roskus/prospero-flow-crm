<?php

declare(strict_types=1);

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
            'from' => $this->faker->email(),
            'subject' => $this->faker->title(),
            'body' => $this->faker->text(),
            'lang' => 'en', // fake()->randomElement(array_keys(config('app.locales'))),
            'version' => 1,
        ];
    }
}
