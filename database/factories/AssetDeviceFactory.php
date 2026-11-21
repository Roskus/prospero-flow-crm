<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Asset;
use App\Models\AssetDevice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssetDevice>
 */
class AssetDeviceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'asset_id' => Asset::factory(),
            'hostname' => fake()->unique()->domainWord(),
            'ip_address' => fake()->localIpv4(),
            'mac_address' => fake()->macAddress(),
            'operating_system' => 'Linux',
            'cpu' => 'Intel Core i5',
            'ram' => '16 GB',
            'storage' => '512 GB SSD',
        ];
    }

    public function mobileLine(): static
    {
        return $this->state([
            'imei' => fake()->unique()->numerify('35#############'),
            'phone_number' => fake()->numerify('+346########'),
        ]);
    }
}
