<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AssetCategory;
use App\Enums\AssetType;
use App\Models\Asset;
use App\Models\AssetMachinery;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssetMachinery>
 */
class AssetMachineryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'asset_id' => Asset::factory()->state(['category' => AssetCategory::Vehicle, 'type' => AssetType::Crane]),
            'capacity' => fake()->numberBetween(5, 100).' t',
            'certification_number' => fake()->bothify('CERT-####-####'),
            'certified_by' => fake()->company(),
            'certification_expires_at' => today()->addYear(),
            'calibration_expires_at' => today()->addYear(),
            'safety_inspection_expires_at' => today()->addYear(),
        ];
    }
}
