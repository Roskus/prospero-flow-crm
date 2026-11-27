<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Asset;

use App\Enums\AssetType;
use App\Models\Asset;
use App\Models\AssetMachinery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AssetMachineryTest extends TestCase
{
    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function craneData(array $overrides = []): array
    {
        return [
            'category' => 'vehicle',
            'type' => 'crane',
            'code' => 'CRN-001',
            'name' => 'Grúa 25 t',
            'usage_hours' => 3200,
            'capacity' => '25 t',
            'certification_number' => 'CERT-2026-0042',
            'certified_by' => 'Bureau Veritas',
            'certification_expires_at' => '2027-03-01',
            'calibration_expires_at' => '2027-01-15',
            'safety_inspection_expires_at' => '2026-12-31',
            ...$overrides,
        ];
    }

    #[Test]
    public function the_vehicle_form_offers_the_new_machinery_types_and_the_machinery_card(): void
    {
        $this->get('/inventory/vehicle/create')
            ->assertOk()
            ->assertSee(AssetType::Excavator->label())
            ->assertSee(AssetType::Generator->label())
            ->assertSee(AssetType::Compressor->label())
            ->assertSee('id="machinery-data"', false)
            ->assertSee(__('Machinery data'));

        $this->get('/inventory/device/create')->assertOk()->assertDontSee('id="machinery-data"', false);
    }

    #[Test]
    public function it_saves_a_crane_with_its_machinery_and_vehicle_data(): void
    {
        $this->post('/inventory/save', $this->craneData())->assertSessionHasNoErrors();

        $crane = Asset::with('vehicle', 'machinery')->firstOrFail();
        $this->assertTrue($crane->isMachinery());
        $this->assertSame(3200, $crane->vehicle->usage_hours);
        $this->assertSame('25 t', $crane->machinery->capacity);
        $this->assertSame('CERT-2026-0042', $crane->machinery->certification_number);
        $this->assertSame('Bureau Veritas', $crane->machinery->certified_by);
        $this->assertSame('2027-03-01', $crane->machinery->certification_expires_at->toDateString());
        $this->assertSame('2027-01-15', $crane->machinery->calibration_expires_at->toDateString());
        $this->assertSame('2026-12-31', $crane->machinery->safety_inspection_expires_at->toDateString());
    }

    #[Test]
    public function the_detail_page_shows_the_machinery_data_and_its_due_dates(): void
    {
        $crane = Asset::factory()->forCompany($this->user->company_id)->machinery(AssetType::Crane, [
            'capacity' => '25 t',
            'certification_number' => 'CERT-77',
            'certified_by' => 'Bureau Veritas',
            'certification_expires_at' => today()->addMonths(6),
        ])->create();

        $this->get(route('asset.show', $crane->id))
            ->assertOk()
            ->assertSeeText(__('Machinery data'))
            ->assertSeeText(['25 t', 'CERT-77', 'Bureau Veritas'])
            ->assertSeeText([__('Certification'), __('Calibration'), __('Safety inspection')])
            ->assertSeeText(today()->addMonths(6)->format('d/m/Y'));
    }

    #[Test]
    public function a_car_has_no_machinery_detail_and_ignores_machinery_fields(): void
    {
        $this->post('/inventory/save', $this->craneData(['type' => 'car', 'code' => 'CAR-001']))->assertSessionHasNoErrors();

        $car = Asset::firstOrFail();
        $this->assertFalse($car->isMachinery());
        $this->assertNull($car->machinery);
        $this->assertSame(0, AssetMachinery::count());

        $this->get(route('asset.show', $car->id))->assertDontSeeText(__('Machinery data'));
    }

    #[Test]
    public function changing_a_machine_to_a_non_machinery_type_removes_its_machinery_detail(): void
    {
        $asset = Asset::factory()->forCompany($this->user->company_id)->machinery(AssetType::Forklift)->create(['code' => 'FRK-001']);

        $this->post('/inventory/save', ['id' => $asset->id, 'category' => 'vehicle', 'type' => 'truck', 'code' => 'FRK-001', 'name' => $asset->name])
            ->assertSessionHasNoErrors();

        $this->assertSame(AssetType::Truck, $asset->fresh()->type);
        $this->assertSame(0, AssetMachinery::count());
    }

    #[Test]
    public function editing_a_machine_without_machinery_fields_clears_them(): void
    {
        $asset = Asset::factory()->forCompany($this->user->company_id)->machinery(AssetType::Generator, ['capacity' => '100 kVA'])->create(['code' => 'GEN-001']);

        $this->post('/inventory/save', ['id' => $asset->id, 'category' => 'vehicle', 'type' => 'generator', 'code' => 'GEN-001', 'name' => $asset->name])
            ->assertSessionHasNoErrors();

        $this->assertNull($asset->machinery()->first()->capacity);
    }

    #[Test]
    public function machinery_fields_are_validated(): void
    {
        $this->post('/inventory/save', $this->craneData([
            'capacity' => str_repeat('x', 51),
            'certification_number' => str_repeat('x', 101),
            'certified_by' => str_repeat('x', 101),
            'certification_expires_at' => 'not-a-date',
            'calibration_expires_at' => 'not-a-date',
            'safety_inspection_expires_at' => 'not-a-date',
        ]))->assertSessionHasErrors([
            'capacity', 'certification_number', 'certified_by',
            'certification_expires_at', 'calibration_expires_at', 'safety_inspection_expires_at',
        ]);

        $this->assertSame(0, Asset::count());
    }

    #[Test]
    public function machinery_due_dates_are_flagged_and_filterable(): void
    {
        $expiredCertification = Asset::factory()->forCompany($this->user->company_id)
            ->machinery(AssetType::Crane, ['certification_expires_at' => today()->subDay()])
            ->create(['name' => 'Grúa certificado vencido']);
        $calibrationSoon = Asset::factory()->forCompany($this->user->company_id)
            ->machinery(AssetType::Compressor, ['calibration_expires_at' => today()->addDays(10)])
            ->create(['name' => 'Compresor calibración pronto']);
        $inspectionSoon = Asset::factory()->forCompany($this->user->company_id)
            ->machinery(AssetType::Excavator, ['safety_inspection_expires_at' => today()->addDays(5)])
            ->create(['name' => 'Excavadora inspección pronto']);
        $fine = Asset::factory()->forCompany($this->user->company_id)
            ->machinery(AssetType::Lift)
            ->create(['name' => 'Elevador en regla']);

        $this->assertSame(Asset::EXPIRED, $expiredCertification->expiryState());
        $this->assertSame(Asset::EXPIRING, $calibrationSoon->expiryState());
        $this->assertSame(Asset::EXPIRING, $inspectionSoon->expiryState());
        $this->assertNull($fine->expiryState());

        $this->get('/inventory/vehicle?expiring=1')
            ->assertSeeText(['Grúa certificado vencido', 'Compresor calibración pronto', 'Excavadora inspección pronto'])
            ->assertDontSeeText('Elevador en regla');
    }

    #[Test]
    public function the_listing_finds_a_machine_by_its_certification_number(): void
    {
        Asset::factory()->forCompany($this->user->company_id)->machinery(AssetType::Crane, ['certification_number' => 'CERT-UNIQUE-9'])->create(['name' => 'Grúa certificada']);
        Asset::factory()->forCompany($this->user->company_id)->vehicle(AssetType::Car)->create(['name' => 'Coche']);

        $this->get('/inventory/vehicle?search=UNIQUE-9')->assertSeeText('Grúa certificada')->assertDontSeeText('Coche');
    }

    #[Test]
    public function the_generated_code_uses_the_machinery_prefix(): void
    {
        $this->getJson('/inventory/code/next?type=excavator')->assertOk()->assertJsonPath('code', 'EXC-00001');
    }
}
