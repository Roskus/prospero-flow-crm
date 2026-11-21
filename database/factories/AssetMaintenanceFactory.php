<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\MaintenanceType;
use App\Models\Asset;
use App\Models\AssetMaintenance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssetMaintenance>
 */
class AssetMaintenanceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'asset_id' => Asset::factory(),
            'type' => MaintenanceType::Preventive,
            'date' => today()->subWeek(),
            'description' => fake()->sentence(),
            'cost' => fake()->randomFloat(2, 50, 1000),
            'currency' => 'EUR',
            'next_due_at' => null,
        ];
    }
}
