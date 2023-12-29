<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Calendar>
 */
class CalendarFactory extends Factory
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
            'user_id' => User::factory(),
            'start_date' => Carbon::tomorrow(),
            'end_date' => Carbon::tomorrow()->addDay(),
            'is_all_day' => fake()->boolean(),
            'title' => fake()->sentence(),
            'description' => fake()->text(),
            'guests' => fake()->randomElements(['a@a.com', 'b@b.com', 'c@c.com', 'd@d.com'], 3),
            'meeting' => fake()->url(),
            'address' => fake()->address(),
            'latitude' => fake()->randomFloat(6, 40.3, 40.5),
            'longitude' => fake()->randomFloat(6, -3.5, -3.9),
        ];
    }
}
