<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Enums\AssetCategory;
use App\Enums\AssetType;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetMaintenance;
use App\Models\Carrier;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AssetTest extends TestCase
{
    #[Test]
    public function a_vehicle_has_its_vehicle_detail_and_a_device_its_device_detail(): void
    {
        $crane = Asset::factory()->vehicle(AssetType::Crane, ['license_plate' => 'GR1234'])->create();
        $mobile = Asset::factory()->device(AssetType::Mobile)->create();

        $this->assertSame(AssetCategory::Vehicle, $crane->category);
        $this->assertSame('GR1234', $crane->vehicle->license_plate);
        $this->assertNull($crane->device);
        $this->assertNotNull($mobile->device);
        $this->assertNull($mobile->vehicle);
    }

    #[Test]
    public function the_current_assignment_is_the_open_one(): void
    {
        $ana = User::factory()->create();
        $luis = User::factory()->create();
        $asset = Asset::factory()->assignedTo($luis)->create();
        AssetAssignment::factory()->closed()->create(['asset_id' => $asset->id, 'user_id' => $ana->id]);

        $this->assertSame($luis->id, $asset->currentAssignment->user_id);
        $this->assertCount(2, $asset->assignments);
        $this->assertSame([$asset->id], Asset::assignedTo($luis->id)->pluck('id')->all());
        $this->assertSame([], Asset::assignedTo($ana->id)->pluck('id')->all());
    }

    #[Test]
    public function deleted_users_and_carriers_still_resolve_in_the_history(): void
    {
        $ana = User::factory()->create(['first_name' => 'Ana']);
        $carrier = Carrier::factory()->create(['name' => 'Orange']);
        $asset = Asset::factory()->device(AssetType::Mobile, ['carrier_id' => $carrier->id])->create();
        $assignment = AssetAssignment::factory()->closed()->create(['asset_id' => $asset->id, 'user_id' => $ana->id]);

        $ana->delete();
        $carrier->delete();

        $this->assertSame('Ana', $assignment->fresh()->user->first_name);
        $this->assertSame('Orange', $asset->fresh()->device->carrier->name);
    }

    #[Test]
    public function it_has_maintenance_records(): void
    {
        $asset = Asset::factory()->create();
        AssetMaintenance::factory()->count(2)->create(['asset_id' => $asset->id]);

        $this->assertCount(2, $asset->maintenances);
    }

    #[Test]
    public function the_renting_end_is_a_due_date_flagged_when_it_is_near(): void
    {
        $endingSoon = Asset::factory()->forCompany($this->user->company_id)->vehicle()->renting(today()->addDays(10)->toDateString())->create();
        $endingLater = Asset::factory()->forCompany($this->user->company_id)->vehicle()->renting(today()->addYear()->toDateString())->create();

        $this->assertSame(Asset::EXPIRING, $endingSoon->expiryState());
        $this->assertNull($endingLater->expiryState());
        $this->assertSame([$endingSoon->id], Asset::ofCompany($this->user->company_id)->expiringSoon()->pluck('id')->all());
    }

    #[Test]
    public function the_age_counts_from_the_purchase_or_the_renting_start(): void
    {
        $this->travelTo('2026-11-21');
        $company = $this->user->company_id;

        $purchased = Asset::factory()->forCompany($company)->device()->create(['purchase_date' => '2023-05-03']);
        $rented = Asset::factory()->forCompany($company)->device()->renting()->create(['rental_starts_at' => '2025-11-21', 'purchase_date' => null]);
        $recent = Asset::factory()->forCompany($company)->device()->create(['purchase_date' => '2026-11-10']);

        $this->assertSame('3 years 6 months', $purchased->age());
        $this->assertSame('1 year', $rented->age());
        $this->assertSame(__('Less than a month'), $recent->age());
    }

    #[Test]
    public function a_vehicle_ages_from_its_manufacturing_year_even_if_bought_later(): void
    {
        $this->travelTo('2026-11-21');
        $company = $this->user->company_id;

        $usedCar = Asset::factory()->forCompany($company)->vehicle(AssetType::Car, ['manufacturing_year' => 2006])->create(['purchase_date' => '2019-10-01']);
        $carWithoutYear = Asset::factory()->forCompany($company)->vehicle(AssetType::Car, ['manufacturing_year' => null])->create(['purchase_date' => '2019-10-01']);
        $device = Asset::factory()->forCompany($company)->device()->create(['purchase_date' => null]);
        $future = Asset::factory()->forCompany($company)->device()->create(['purchase_date' => '2027-01-01']);

        $this->assertTrue($usedCar->agesFromManufacturingYear());
        $this->assertSame('2006-01-01', $usedCar->ageStartDate()->toDateString());
        $this->assertSame('20 years 10 months', $usedCar->age());
        $this->assertFalse($carWithoutYear->agesFromManufacturingYear());
        $this->assertSame('7 years 1 month', $carWithoutYear->age());
        $this->assertNull($device->age());
        $this->assertNull($future->age());
    }
}
