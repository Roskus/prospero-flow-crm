<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Asset;

use App\Enums\AssetStatus;
use App\Enums\AssetType;
use App\Models\Asset;
use App\Models\AssetMaintenance;
use App\Models\Supplier;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AssetMaintenanceControllerTest extends TestCase
{
    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function maintenanceData(Asset $asset, array $overrides = []): array
    {
        return [
            'asset_id' => $asset->id,
            'type' => 'preventive',
            'date' => today()->toDateString(),
            'description' => 'Cambio de aceite',
            ...$overrides,
        ];
    }

    #[Test]
    public function it_logs_a_maintenance_record(): void
    {
        $van = Asset::factory()->forCompany($this->user->company_id)->vehicle(AssetType::Van)->create();

        $this->get('/inventory/maintenance/create/'.$van->id)->assertOk()->assertSee(__('Odometer (km)'));
        $this->post('/inventory/maintenance/save', $this->maintenanceData($van, [
            'cost' => '250',
            'currency' => 'eur',
            'next_due_at' => today()->addMonths(6)->toDateString(),
        ]))->assertRedirect(route('asset.show', $van->id));

        $record = $van->maintenances()->firstOrFail();
        $this->assertSame('250.00', $record->cost);
        $this->assertSame('EUR', $record->currency);
        $this->assertSame($this->user->id, $record->created_by);
        $this->get(route('asset.show', $van->id))->assertSeeText('Cambio de aceite');
    }

    #[Test]
    public function type_date_and_a_coherent_next_due_date_are_required(): void
    {
        $van = Asset::factory()->forCompany($this->user->company_id)->vehicle()->create();

        $this->post('/inventory/maintenance/save', $this->maintenanceData($van, ['type' => '', 'date' => '']))
            ->assertSessionHasErrors(['type', 'date']);
        $this->post('/inventory/maintenance/save', $this->maintenanceData($van, ['next_due_at' => today()->subDay()->toDateString()]))
            ->assertSessionHasErrors('next_due_at');
        $this->post('/inventory/maintenance/save', $this->maintenanceData($van, ['supplier_id' => Supplier::factory()->create()->id]))
            ->assertSessionHasErrors('supplier_id');

        $this->assertDatabaseCount('asset_maintenance', 0);
    }

    #[Test]
    public function a_higher_reading_raises_the_vehicle_odometer_and_a_lower_one_does_not(): void
    {
        $car = Asset::factory()->forCompany($this->user->company_id)->vehicle(AssetType::Car, ['odometer_km' => 80000])->create();

        $this->post('/inventory/maintenance/save', $this->maintenanceData($car, ['odometer_km' => 85000]));
        $this->assertSame(85000, $car->vehicle->fresh()->odometer_km);

        $this->post('/inventory/maintenance/save', $this->maintenanceData($car, ['odometer_km' => 70000]));
        $this->assertSame(85000, $car->vehicle->fresh()->odometer_km);
    }

    #[Test]
    public function a_higher_reading_raises_machinery_usage_hours(): void
    {
        $lift = Asset::factory()->forCompany($this->user->company_id)->vehicle(AssetType::Lift, ['odometer_km' => null, 'usage_hours' => 1000])->create();

        $this->post('/inventory/maintenance/save', $this->maintenanceData($lift, ['usage_hours' => 1200]));

        $this->assertSame(1200, $lift->vehicle->fresh()->usage_hours);
        $this->assertNull($lift->vehicle->fresh()->odometer_km);
    }

    #[Test]
    public function the_history_shows_records_newest_first_with_totals_per_currency(): void
    {
        $crane = Asset::factory()->forCompany($this->user->company_id)->vehicle(AssetType::Crane)->create();
        AssetMaintenance::factory()->create(['asset_id' => $crane->id, 'date' => today()->subMonths(2), 'description' => 'Revisión antigua', 'cost' => 100, 'currency' => 'EUR']);
        AssetMaintenance::factory()->create(['asset_id' => $crane->id, 'date' => today()->subWeek(), 'description' => 'Revisión reciente', 'cost' => 150, 'currency' => 'EUR']);
        AssetMaintenance::factory()->create(['asset_id' => $crane->id, 'date' => today()->subDays(3), 'description' => 'Repuesto importado', 'cost' => 40, 'currency' => 'USD']);

        $this->get(route('asset.show', $crane->id))
            ->assertSeeTextInOrder(['Repuesto importado', 'Revisión reciente', 'Revisión antigua'])
            ->assertSeeText('250,00 EUR')
            ->assertSeeText('40,00 USD');
    }

    #[Test]
    public function the_next_maintenance_follows_the_latest_record_and_feeds_the_expiry_flags(): void
    {
        $crane = Asset::factory()->forCompany($this->user->company_id)->vehicle(AssetType::Crane, ['insurance_expires_at' => null, 'inspection_expires_at' => null])->create();

        $this->post('/inventory/maintenance/save', $this->maintenanceData($crane, ['date' => today()->subMonth()->toDateString(), 'next_due_at' => today()->addYear()->toDateString()]));
        $this->post('/inventory/maintenance/save', $this->maintenanceData($crane, ['next_due_at' => today()->addDays(15)->toDateString()]));

        $crane->refresh();
        $this->assertTrue($crane->next_maintenance_at->equalTo(today()->addDays(15)));
        $this->assertSame(Asset::EXPIRING, $crane->expiryState());
        $this->get('/inventory/vehicle?expiring=1')->assertSeeText($crane->name);
    }

    #[Test]
    public function editing_and_deleting_records_recalculates_next_maintenance_and_totals(): void
    {
        $crane = Asset::factory()->forCompany($this->user->company_id)->vehicle(AssetType::Crane)->create();
        $old = AssetMaintenance::factory()->create(['asset_id' => $crane->id, 'date' => today()->subMonths(3), 'cost' => 100, 'next_due_at' => today()->addMonths(9)]);
        $latest = AssetMaintenance::factory()->create(['asset_id' => $crane->id, 'date' => today()->subWeek(), 'cost' => 150, 'next_due_at' => today()->addDays(5)]);

        $this->get('/inventory/maintenance/update/'.$latest->id)->assertOk()->assertSee('value="150.00"', false);
        $this->post('/inventory/maintenance/save', $this->maintenanceData($crane, ['id' => $latest->id, 'date' => today()->subWeek()->toDateString(), 'cost' => 200, 'currency' => 'EUR', 'next_due_at' => today()->addDays(20)->toDateString()]))
            ->assertSessionHasNoErrors();
        $this->assertTrue($crane->fresh()->next_maintenance_at->equalTo(today()->addDays(20)));

        $this->delete('/inventory/maintenance/delete/'.$latest->id)->assertRedirect(route('asset.show', $crane->id));

        $this->assertModelMissing($latest);
        $this->assertTrue($crane->fresh()->next_maintenance_at->equalTo($old->next_due_at));
        $this->get(route('asset.show', $crane->id))->assertSeeText('100,00 EUR')->assertDontSeeText('300,00');
    }

    #[Test]
    public function the_asset_can_go_to_maintenance_and_back_from_the_form(): void
    {
        $crane = Asset::factory()->forCompany($this->user->company_id)->vehicle(AssetType::Crane)->create();
        $data = ['id' => $crane->id, 'category' => 'vehicle', 'type' => 'crane', 'code' => $crane->code, 'name' => $crane->name];

        $this->post('/inventory/save', [...$data, 'status' => 'in_maintenance'])->assertSessionHasNoErrors();
        $this->assertSame(AssetStatus::InMaintenance, $crane->fresh()->status);

        $this->post('/inventory/save', [...$data, 'status' => 'available'])->assertSessionHasNoErrors();
        $this->assertSame(AssetStatus::Available, $crane->fresh()->status);
    }

    #[Test]
    public function maintenance_routes_do_not_reach_other_companies(): void
    {
        $other = Asset::factory()->vehicle()->create();
        $record = AssetMaintenance::factory()->create(['asset_id' => $other->id]);
        $mine = Asset::factory()->forCompany($this->user->company_id)->vehicle()->create();

        $this->get('/inventory/maintenance/create/'.$other->id)->assertNotFound();
        $this->get('/inventory/maintenance/update/'.$record->id)->assertNotFound();
        $this->post('/inventory/maintenance/save', $this->maintenanceData($other))->assertNotFound();
        $this->post('/inventory/maintenance/save', $this->maintenanceData($mine, ['id' => $record->id]))->assertNotFound();
        $this->delete('/inventory/maintenance/delete/'.$record->id)->assertNotFound();

        $this->assertModelExists($record);
    }
}
