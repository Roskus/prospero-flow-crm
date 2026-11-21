<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\FuelType;
use App\Models\Asset;
use App\Models\AssetVehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssetVehicle>
 */
class AssetVehicleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'asset_id' => Asset::factory(),
            'license_plate' => fake()->unique()->bothify('####???'),
            'vin' => strtoupper(fake()->bothify('?????????????????')),
            'manufacturing_year' => fake()->numberBetween(2010, 2026),
            'fuel_type' => FuelType::Diesel,
            'odometer_km' => fake()->numberBetween(0, 200000),
            'usage_hours' => null,
            'insurance_company' => fake()->company(),
            'insurance_policy' => fake()->bothify('POL-######'),
            'insurance_expires_at' => today()->addYear(),
            'inspection_expires_at' => today()->addYear(),
        ];
    }
}
