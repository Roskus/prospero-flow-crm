<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Campaign>
 */
class CampaignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => fake()->words(3, true),
            'subject' => fake()->sentence(),
            'from' => fake()->safeEmail(),
            'body' => '<p>'.fake()->paragraph().'</p>',
            'tags' => null,
            'schedule_send_date' => now()->toDateString(),
            'schedule_send_time' => null,
            'schedule_at' => now()->startOfDay(),
            'send_at' => null,
            'emails_count' => null,
        ];
    }

    public function sent(): static
    {
        return $this->state(['send_at' => now(), 'emails_count' => 0]);
    }
}
