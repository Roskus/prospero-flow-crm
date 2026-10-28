<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\CampaignRecipient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CampaignRecipient>
 */
class CampaignRecipientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'campaign_id' => Campaign::factory()->sent(),
            'lead_id' => null,
            'email' => fake()->unique()->safeEmail(),
            'status' => CampaignRecipient::STATUS_SENT,
            'error' => null,
            'sent_at' => now(),
            'opened_at' => null,
            'open_count' => 0,
        ];
    }

    public function bounced(): static
    {
        return $this->state(['status' => CampaignRecipient::STATUS_BOUNCED, 'error' => '550 Mailbox not found']);
    }

    public function opened(int $times = 1): static
    {
        return $this->state(['opened_at' => now(), 'open_count' => $times]);
    }
}
