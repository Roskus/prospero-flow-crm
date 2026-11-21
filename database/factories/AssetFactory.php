<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AcquisitionType;
use App\Enums\AssetCategory;
use App\Enums\AssetStatus;
use App\Enums\AssetType;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetDevice;
use App\Models\AssetVehicle;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asset>
 */
class AssetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'category' => AssetCategory::Device,
            'type' => AssetType::Notebook,
            'status' => AssetStatus::Available,
            'code' => fake()->unique()->bothify('AS-####'),
            'name' => fake()->words(2, true),
            'brand' => fake()->company(),
            'model' => fake()->bothify('Model-##'),
            'serial_number' => fake()->bothify('SN-########'),
        ];
    }

    public function forCompany(Company|int $company): static
    {
        return $this->state(['company_id' => $company instanceof Company ? $company->id : $company]);
    }

    /**
     * @param  array<string, mixed>  $detail
     */
    public function vehicle(AssetType $type = AssetType::Car, array $detail = []): static
    {
        return $this->state(['category' => AssetCategory::Vehicle, 'type' => $type])
            ->has(AssetVehicle::factory()->state($detail), 'vehicle');
    }

    /**
     * @param  array<string, mixed>  $detail
     */
    public function device(AssetType $type = AssetType::Notebook, array $detail = []): static
    {
        return $this->state(['category' => AssetCategory::Device, 'type' => $type])
            ->has(AssetDevice::factory()->state($detail), 'device');
    }

    public function renting(?string $endsAt = null): static
    {
        return $this->state([
            'acquisition_type' => AcquisitionType::Renting,
            'rental_monthly_fee' => fake()->randomFloat(2, 50, 2000),
            'rental_starts_at' => today()->subYear(),
            'rental_ends_at' => $endsAt ?? today()->addYears(2),
        ]);
    }

    public function retired(): static
    {
        return $this->state(['status' => AssetStatus::Retired]);
    }

    public function inMaintenance(): static
    {
        return $this->state(['status' => AssetStatus::InMaintenance]);
    }

    public function assignedTo(User $user): static
    {
        return $this->state(['status' => AssetStatus::Assigned])
            ->has(AssetAssignment::factory()->state(['user_id' => $user->id]), 'assignments');
    }
}
