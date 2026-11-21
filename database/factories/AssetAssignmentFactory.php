<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssetAssignment>
 */
class AssetAssignmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'asset_id' => Asset::factory(),
            'user_id' => User::factory(),
            'start_date' => today()->subMonth(),
            'end_date' => null,
        ];
    }

    public function closed(): static
    {
        return $this->state(['end_date' => today()->subDay()]);
    }
}
