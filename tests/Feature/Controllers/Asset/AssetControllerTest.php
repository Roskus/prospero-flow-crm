<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Asset;

use App\Enums\AssetStatus;
use App\Enums\AssetType;
use App\Models\Asset;
use App\Models\Branch;
use App\Models\Carrier;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AssetControllerTest extends TestCase
{
    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function vehicleData(array $overrides = []): array
    {
        return [
            'category' => 'vehicle',
            'type' => 'car',
            'code' => 'CAR-001',
            'name' => 'Furgoneta reparto',
            ...$overrides,
        ];
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function mobileData(array $overrides = []): array
    {
        return [
            'category' => 'device',
            'type' => 'mobile',
            'code' => 'MOB-001',
            'name' => 'iPhone ventas',
            'imei' => '356938035643809',
            ...$overrides,
        ];
    }

    #[Test]
    public function it_creates_a_vehicle_with_required_data_only_as_available(): void
    {
        $this->get('/inventory/vehicle/create')->assertOk()->assertSee(AssetType::Crane->label());

        $response = $this->post('/inventory/save', $this->vehicleData(['code' => ' car-001 ']));

        $asset = Asset::firstOrFail();
        $response->assertRedirect(route('asset.show', $asset->id));
        $this->assertSame($this->user->company_id, $asset->company_id);
        $this->assertSame('CAR-001', $asset->code);
        $this->assertSame(AssetStatus::Available, $asset->status);
        $this->assertNotNull($asset->vehicle);
    }

    #[Test]
    public function it_saves_vehicle_data_and_machinery_usage_hours(): void
    {
        $this->post('/inventory/save', $this->vehicleData([
            'license_plate' => '1234 abc',
            'fuel_type' => 'diesel',
            'insurance_expires_at' => '2027-03-01',
        ]))->assertSessionHasNoErrors();
        $this->post('/inventory/save', $this->vehicleData(['type' => 'lift', 'code' => 'LIFT-1', 'usage_hours' => 1500]))->assertSessionHasNoErrors();

        $car = Asset::where('code', 'CAR-001')->firstOrFail();
        $this->get(route('asset.show', $car->id))
            ->assertOk()
            ->assertSeeText('1234ABC')
            ->assertSeeText(__('Diesel'))
            ->assertSeeText('01/03/2027');

        $lift = Asset::where('code', 'LIFT-1')->firstOrFail();
        $this->assertSame(1500, $lift->vehicle->usage_hours);
        $this->assertNull($lift->vehicle->odometer_km);
    }

    #[Test]
    public function it_saves_device_data_and_validates_ip_and_mac(): void
    {
        $this->post('/inventory/save', [
            'category' => 'device', 'type' => 'notebook', 'code' => 'NB-001', 'name' => 'Notebook ventas',
            'hostname' => 'nb-ventas-01', 'ram' => '16 GB', 'operating_system' => 'Windows 11',
            'ip_address' => '192.168.1.20', 'mac_address' => '00:1A:2B:3C:4D:5E',
        ])->assertSessionHasNoErrors();

        $notebook = Asset::where('code', 'NB-001')->firstOrFail();
        $this->get(route('asset.show', $notebook->id))
            ->assertSeeText('nb-ventas-01')
            ->assertSeeText('16 GB')
            ->assertSeeText('Windows 11')
            ->assertDontSeeText(__('License plate'));

        $this->post('/inventory/save', ['category' => 'device', 'type' => 'router', 'code' => 'R-1', 'name' => 'Router', 'ip_address' => '999.1.1.1'])
            ->assertSessionHasErrors('ip_address');
        $this->post('/inventory/save', ['category' => 'device', 'type' => 'router', 'code' => 'R-1', 'name' => 'Router', 'mac_address' => 'zz:zz'])
            ->assertSessionHasErrors('mac_address');
    }

    #[Test]
    public function the_type_must_belong_to_the_category(): void
    {
        $this->post('/inventory/save', ['category' => 'device', 'type' => 'crane', 'code' => 'X1', 'name' => 'Bad'])
            ->assertSessionHasErrors('type');
        $this->post('/inventory/save', ['category' => 'boat', 'type' => 'car', 'code' => 'X1', 'name' => 'Bad'])
            ->assertSessionHasErrors('category');

        $this->assertDatabaseCount('asset', 0);
    }

    #[Test]
    public function name_and_code_are_required(): void
    {
        $this->post('/inventory/save', $this->vehicleData(['name' => '', 'code' => '']))
            ->assertSessionHasErrors(['name', 'code']);
    }

    #[Test]
    public function the_code_is_unique_among_the_non_deleted_assets_of_the_company(): void
    {
        Asset::factory()->forCompany($this->user->company_id)->create(['code' => 'DUP-1']);
        Asset::factory()->forCompany($this->user->company_id)->create(['code' => 'DEL-1'])->delete();
        Asset::factory()->create(['code' => 'OTHER-1']);

        $this->post('/inventory/save', $this->vehicleData(['code' => 'DUP-1']))->assertSessionHasErrors('code');
        $this->post('/inventory/save', $this->vehicleData(['code' => 'DEL-1']))->assertSessionHasNoErrors();
        $this->post('/inventory/save', $this->vehicleData(['code' => 'OTHER-1']))->assertSessionHasNoErrors();
    }

    #[Test]
    public function it_updates_an_asset_keeping_its_category(): void
    {
        $asset = Asset::factory()->forCompany($this->user->company_id)->vehicle(AssetType::Car)->create(['code' => 'CAR-9']);

        $this->get('/inventory/update/'.$asset->id)->assertOk()->assertSee('value="CAR-9"', false);
        $this->post('/inventory/save', $this->vehicleData(['id' => $asset->id, 'category' => 'device', 'type' => 'van', 'code' => 'CAR-9', 'name' => 'Renamed']))
            ->assertSessionHasNoErrors();

        $asset->refresh();
        $this->assertSame('Renamed', $asset->name);
        $this->assertSame(AssetType::Van, $asset->type);
        $this->assertTrue($asset->isVehicle());
    }

    #[Test]
    public function a_mobile_stores_imei_carrier_and_number(): void
    {
        $carrier = Carrier::factory()->forCompany($this->user->company_id)->create(['name' => 'Movistar']);

        $this->post('/inventory/save', $this->mobileData(['carrier_id' => $carrier->id, 'phone_number' => '+34 600 123 456']))
            ->assertSessionHasNoErrors();

        $mobile = Asset::firstOrFail();
        $this->assertSame('+34600123456', $mobile->device->phone_number);
        $this->get(route('asset.show', $mobile->id))->assertSeeText(['356938035643809', 'Movistar', '+34600123456']);
        $this->get('/inventory/device')->assertSeeText(['356938035643809', 'Movistar', '+34600123456']);
    }

    #[Test]
    public function a_mobile_requires_a_valid_and_unique_imei_but_not_a_line(): void
    {
        $this->post('/inventory/save', $this->mobileData(['imei' => '']))->assertSessionHasErrors('imei');
        $this->post('/inventory/save', $this->mobileData(['imei' => '12345']))->assertSessionHasErrors('imei');
        $this->post('/inventory/save', $this->mobileData())->assertSessionHasNoErrors();
        $this->post('/inventory/save', $this->mobileData(['code' => 'MOB-002']))->assertSessionHasErrors('imei');

        $this->assertNull(Asset::firstOrFail()->device->carrier_id);
    }

    #[Test]
    public function the_same_imei_can_exist_in_another_company_and_editing_keeps_it(): void
    {
        Asset::factory()->device(AssetType::Mobile, ['imei' => '356938035643809'])->create();

        $this->post('/inventory/save', $this->mobileData())->assertSessionHasNoErrors();
        $mine = Asset::ofCompany($this->user->company_id)->firstOrFail();
        $this->post('/inventory/save', $this->mobileData(['id' => $mine->id, 'name' => 'Renamed']))->assertSessionHasNoErrors();
    }

    #[Test]
    public function the_carrier_must_be_a_non_deleted_carrier_of_the_company(): void
    {
        $foreign = Carrier::factory()->create();
        $deleted = Carrier::factory()->forCompany($this->user->company_id)->create();
        $deleted->delete();

        $this->post('/inventory/save', $this->mobileData(['carrier_id' => $foreign->id]))->assertSessionHasErrors('carrier_id');
        $this->post('/inventory/save', $this->mobileData(['carrier_id' => $deleted->id]))->assertSessionHasErrors('carrier_id');
    }

    #[Test]
    public function a_deleted_carrier_keeps_showing_on_its_devices_but_is_not_selectable(): void
    {
        $carrier = Carrier::factory()->forCompany($this->user->company_id)->create(['name' => 'Orange']);
        $mobile = Asset::factory()->forCompany($this->user->company_id)->device(AssetType::Mobile, ['imei' => '356938035643809', 'carrier_id' => $carrier->id])->create();
        $carrier->delete();

        $this->get(route('asset.show', $mobile->id))->assertSeeText('Orange');
        $this->get('/inventory/device/create')->assertDontSeeText('Orange');
    }

    #[Test]
    public function phone_line_fields_are_dropped_for_devices_without_a_line(): void
    {
        $this->post('/inventory/save', ['category' => 'device', 'type' => 'printer', 'code' => 'PR-1', 'name' => 'Printer', 'imei' => '356938035643809', 'phone_number' => '600000000', 'sim_pin' => '1234', 'sim_puk' => '12345678'])
            ->assertSessionHasNoErrors();

        $device = Asset::firstOrFail()->device;
        $this->assertNull($device->imei);
        $this->assertNull($device->phone_number);
        $this->assertNull($device->sim_pin);
        $this->assertNull($device->sim_puk);
    }

    #[Test]
    public function the_sim_pin_and_puk_are_stored_encrypted_and_never_serialized(): void
    {
        $this->post('/inventory/save', $this->mobileData(['sim_pin' => '12 34', 'sim_puk' => '1234 5678']))->assertSessionHasNoErrors();

        $asset = Asset::firstOrFail();
        $this->assertSame('1234', $asset->device->sim_pin);
        $this->assertSame('12345678', $asset->device->sim_puk);

        $raw = DB::table('asset_device')->where('asset_id', $asset->id)->first(['sim_pin', 'sim_puk']);
        $this->assertNotSame('12345678', $raw->sim_puk);
        $this->assertSame('12345678', Crypt::decryptString($raw->sim_puk));
        $this->assertSame('1234', Crypt::decryptString($raw->sim_pin));
        $this->assertArrayNotHasKey('sim_puk', $asset->device->toArray());
        $this->assertArrayNotHasKey('sim_pin', $asset->device->toArray());
    }

    #[Test]
    public function the_sim_codes_must_be_numeric_with_their_length(): void
    {
        $this->post('/inventory/save', $this->mobileData(['sim_pin' => '12', 'sim_puk' => '1234']))->assertSessionHasErrors(['sim_pin', 'sim_puk']);
        $this->post('/inventory/save', $this->mobileData(['sim_pin' => 'abcd', 'sim_puk' => '1234567a']))->assertSessionHasErrors(['sim_pin', 'sim_puk']);
    }

    #[Test]
    public function the_sim_codes_are_only_shown_to_users_who_can_update_assets(): void
    {
        $asset = Asset::factory()->forCompany($this->user->company_id)->device(AssetType::Mobile, ['imei' => '356938035643809', 'sim_puk' => '87654321'])->create();

        $this->get(route('asset.show', $asset->id))->assertOk()->assertSee('87654321');

        $reader = User::factory()->create(['company_id' => $this->user->company_id]);
        $reader->givePermissionTo(Permission::findOrCreate('read asset', 'web'));
        $this->actingAs($reader)->get(route('asset.show', $asset->id))->assertOk()->assertDontSee('87654321')->assertDontSeeText(__('SIM PUK'));
    }

    #[Test]
    public function branch_and_supplier_must_belong_to_the_company(): void
    {
        $foreignBranch = Branch::factory()->create();

        $this->post('/inventory/save', $this->vehicleData(['branch_id' => $foreignBranch->id]))->assertSessionHasErrors('branch_id');
    }

    #[Test]
    public function retiring_hides_the_asset_from_the_default_listing(): void
    {
        $asset = Asset::factory()->forCompany($this->user->company_id)->vehicle()->create(['name' => 'Coche viejo']);

        $this->post('/inventory/save', $this->vehicleData(['id' => $asset->id, 'code' => $asset->code, 'name' => 'Coche viejo', 'status' => 'retired']))
            ->assertSessionHasNoErrors();

        $this->assertSame(AssetStatus::Retired, $asset->fresh()->status);
        $this->get('/inventory/vehicle')->assertDontSeeText('Coche viejo');
        $this->get('/inventory/vehicle?status=retired')->assertSeeText('Coche viejo');
    }

    #[Test]
    public function retiring_an_assigned_asset_closes_its_assignment(): void
    {
        $holder = User::factory()->create(['company_id' => $this->user->company_id]);
        $asset = Asset::factory()->forCompany($this->user->company_id)->vehicle()->assignedTo($holder)->create();

        $this->post('/inventory/save', $this->vehicleData(['id' => $asset->id, 'code' => $asset->code, 'status' => 'retired']))
            ->assertSessionHasNoErrors();

        $asset->refresh();
        $this->assertSame(AssetStatus::Retired, $asset->status);
        $this->assertNull($asset->currentAssignment);
        $this->assertTrue($asset->assignments()->first()->end_date->isToday());
    }

    #[Test]
    public function the_assigned_status_requires_an_active_user_of_the_company(): void
    {
        $foreignUser = User::factory()->create();
        $inactiveUser = User::factory()->create(['company_id' => $this->user->company_id, 'active' => false]);

        $this->post('/inventory/save', $this->vehicleData(['status' => 'assigned']))->assertSessionHasErrors('assigned_user_id');
        $this->post('/inventory/save', $this->vehicleData(['status' => 'assigned', 'assigned_user_id' => $foreignUser->id]))->assertSessionHasErrors('assigned_user_id');
        $this->post('/inventory/save', $this->vehicleData(['status' => 'assigned', 'assigned_user_id' => $inactiveUser->id]))->assertSessionHasErrors('assigned_user_id');

        $this->assertSame(0, Asset::count());
    }

    #[Test]
    public function a_new_asset_saved_as_assigned_opens_an_assignment_for_the_user(): void
    {
        $holder = User::factory()->create(['company_id' => $this->user->company_id]);

        $this->post('/inventory/save', $this->vehicleData(['status' => 'assigned', 'assigned_user_id' => $holder->id]))->assertSessionHasNoErrors();

        $asset = Asset::firstOrFail();
        $this->assertSame(AssetStatus::Assigned, $asset->status);
        $this->assertSame($holder->id, $asset->currentAssignment->user_id);
        $this->assertSame($this->user->id, $asset->currentAssignment->assigned_by);
        $this->assertTrue($asset->currentAssignment->start_date->isToday());
    }

    #[Test]
    public function changing_the_holder_from_the_form_closes_the_assignment_and_opens_a_new_one(): void
    {
        $holder = User::factory()->create(['company_id' => $this->user->company_id]);
        $newHolder = User::factory()->create(['company_id' => $this->user->company_id]);
        $asset = Asset::factory()->forCompany($this->user->company_id)->vehicle()->assignedTo($holder)->create();
        $data = $this->vehicleData(['id' => $asset->id, 'code' => $asset->code, 'status' => 'assigned']);

        $this->post('/inventory/save', [...$data, 'assigned_user_id' => $holder->id])->assertSessionHasNoErrors();
        $this->assertSame(1, $asset->assignments()->count());

        $this->post('/inventory/save', [...$data, 'assigned_user_id' => $newHolder->id])->assertSessionHasNoErrors();

        $asset->refresh();
        $this->assertSame(2, $asset->assignments()->count());
        $this->assertSame($newHolder->id, $asset->currentAssignment->user_id);
        $this->assertTrue($asset->assignments()->where('user_id', $holder->id)->first()->end_date->isToday());
    }

    #[Test]
    public function leaving_the_assigned_status_returns_the_asset(): void
    {
        $holder = User::factory()->create(['company_id' => $this->user->company_id]);
        $asset = Asset::factory()->forCompany($this->user->company_id)->vehicle()->assignedTo($holder)->create();

        $this->post('/inventory/save', $this->vehicleData(['id' => $asset->id, 'code' => $asset->code, 'status' => 'in_maintenance', 'assigned_user_id' => $holder->id]))
            ->assertSessionHasNoErrors();

        $asset->refresh();
        $this->assertSame(AssetStatus::InMaintenance, $asset->status);
        $this->assertNull($asset->currentAssignment);
        $this->assertTrue($asset->assignments()->first()->end_date->isToday());
    }

    #[Test]
    public function a_deactivated_holder_stays_valid_while_the_asset_keeps_its_assignment(): void
    {
        $holder = User::factory()->create(['company_id' => $this->user->company_id]);
        $asset = Asset::factory()->forCompany($this->user->company_id)->vehicle()->assignedTo($holder)->create();
        $holder->forceFill(['active' => false])->save();

        $this->get('/inventory/update/'.$asset->id)->assertOk()->assertSeeText($holder->getFullName().' ('.__('inactive').')');
        $this->post('/inventory/save', $this->vehicleData(['id' => $asset->id, 'code' => $asset->code, 'name' => 'Renombrado', 'status' => 'assigned', 'assigned_user_id' => $holder->id]))
            ->assertSessionHasNoErrors();

        $this->assertSame(1, $asset->assignments()->count());
        $this->assertSame('Renombrado', $asset->fresh()->name);
    }

    #[Test]
    public function a_renting_asset_keeps_only_the_renting_data(): void
    {
        $this->post('/inventory/save', $this->vehicleData([
            'acquisition_type' => 'renting',
            'purchase_date' => '2026-01-10',
            'purchase_price' => 30000,
            'rental_monthly_fee' => 450.5,
            'rental_starts_at' => '2026-01-01',
            'rental_ends_at' => '2029-12-31',
        ]))->assertSessionHasNoErrors();

        $asset = Asset::firstOrFail();
        $this->assertTrue($asset->isRenting());
        $this->assertNull($asset->purchase_date);
        $this->assertNull($asset->purchase_price);
        $this->assertSame('450.50', $asset->rental_monthly_fee);
        $this->assertSame($this->user->company->currency, $asset->currency);
        $this->get(route('asset.show', $asset->id))->assertOk()->assertSeeText(__('Renting company'))->assertSeeText('31/12/2029');

        $this->post('/inventory/save', $this->vehicleData(['id' => $asset->id, 'code' => $asset->code, 'acquisition_type' => 'purchase', 'purchase_price' => 30000, 'rental_monthly_fee' => 450]))
            ->assertSessionHasNoErrors();
        $asset->refresh();
        $this->assertFalse($asset->isRenting());
        $this->assertNull($asset->rental_monthly_fee);
        $this->assertNull($asset->rental_ends_at);
        $this->assertSame('30000.00', $asset->purchase_price);
    }

    #[Test]
    public function the_renting_end_cannot_be_before_its_start(): void
    {
        $this->post('/inventory/save', $this->vehicleData([
            'acquisition_type' => 'renting',
            'rental_starts_at' => '2026-06-01',
            'rental_ends_at' => '2026-05-01',
        ]))->assertSessionHasErrors('rental_ends_at');

        $this->post('/inventory/save', $this->vehicleData(['acquisition_type' => 'leasing']))->assertSessionHasErrors('acquisition_type');
    }

    #[Test]
    public function an_asset_of_another_company_is_not_found(): void
    {
        $other = Asset::factory()->vehicle()->create(['name' => 'Ajeno']);

        $this->get('/inventory/update/'.$other->id)->assertNotFound();
        $this->get('/inventory/show/'.$other->id)->assertNotFound();
        $this->post('/inventory/save', $this->vehicleData(['id' => $other->id, 'name' => 'PWNED']))->assertNotFound();
        $this->delete('/inventory/delete/'.$other->id)->assertNotFound();
        $this->get('/inventory/photo/'.$other->id)->assertNotFound();

        $this->assertSame('Ajeno', $other->fresh()->name);
    }

    #[Test]
    public function each_listing_shows_only_its_category_of_the_company(): void
    {
        Asset::factory()->forCompany($this->user->company_id)->vehicle(AssetType::Crane)->create(['name' => 'Grúa torre']);
        Asset::factory()->forCompany($this->user->company_id)->device(AssetType::Server)->create(['name' => 'Servidor web']);
        Asset::factory()->vehicle()->create(['name' => 'Coche ajeno']);

        $this->get('/inventory/vehicle')->assertOk()->assertSeeText('Grúa torre')->assertDontSeeText(['Servidor web', 'Coche ajeno']);
        $this->get('/inventory/device')->assertOk()->assertSeeText('Servidor web')->assertDontSeeText(['Grúa torre', 'Coche ajeno']);
        $this->get('/inventory/boat')->assertNotFound();
    }

    #[Test]
    public function the_listing_searches_and_filters(): void
    {
        $holder = User::factory()->create(['company_id' => $this->user->company_id, 'first_name' => 'Ana']);
        $branch = Branch::factory()->forCompany($this->user->company_id)->create();
        Asset::factory()->forCompany($this->user->company_id)->vehicle(AssetType::Car, ['license_plate' => '1234ABC'])->create(['name' => 'Coche Ana', 'branch_id' => $branch->id]);
        Asset::factory()->forCompany($this->user->company_id)->vehicle(AssetType::Crane, ['license_plate' => '9999ZZZ'])->create(['name' => 'Grúa']);
        Asset::factory()->forCompany($this->user->company_id)->device(AssetType::Notebook)->assignedTo($holder)->create(['name' => 'Notebook Ana']);
        Asset::factory()->forCompany($this->user->company_id)->device(AssetType::Mobile, ['imei' => '356938035643809', 'phone_number' => '+34600123456'])->create(['name' => 'Móvil comercial']);

        $this->get('/inventory/vehicle?search=1234ABC')->assertSeeText('Coche Ana')->assertDontSeeText('Grúa');
        $this->get('/inventory/vehicle?type=crane')->assertSeeText('Grúa')->assertDontSeeText('Coche Ana');
        $this->get('/inventory/vehicle?branch_id='.$branch->id)->assertSeeText('Coche Ana')->assertDontSeeText('Grúa');
        $this->get('/inventory/device?user_id='.$holder->id)->assertSeeText('Notebook Ana')->assertDontSeeText('Móvil comercial');
        $this->get('/inventory/device?search=600123456')->assertSeeText('Móvil comercial')->assertDontSeeText('Notebook Ana');
        $this->get('/inventory/device?status=assigned')->assertSeeText('Notebook Ana')->assertDontSeeText('Móvil comercial');
    }

    #[Test]
    public function due_dates_are_flagged_and_filterable(): void
    {
        $soon = Asset::factory()->forCompany($this->user->company_id)->vehicle(AssetType::Car, ['inspection_expires_at' => today()->addDays(10)])->create(['name' => 'ITV pronto']);
        $expired = Asset::factory()->forCompany($this->user->company_id)->vehicle(AssetType::Van, ['insurance_expires_at' => today()->subDay()])->create(['name' => 'Seguro vencido']);
        $fine = Asset::factory()->forCompany($this->user->company_id)->vehicle(AssetType::Truck)->create(['name' => 'Todo en orden']);
        $retired = Asset::factory()->forCompany($this->user->company_id)->vehicle(AssetType::Car, ['insurance_expires_at' => today()->subYear()])->retired()->create();

        $this->assertSame(Asset::EXPIRING, $soon->expiryState());
        $this->assertSame(Asset::EXPIRED, $expired->expiryState());
        $this->assertNull($fine->expiryState());
        $this->assertNull($retired->expiryState());

        $this->get('/inventory/vehicle')->assertSeeText([__('Expiring soon'), __('Expired')]);
        $this->get('/inventory/vehicle?expiring=1')
            ->assertSeeText(['ITV pronto', 'Seguro vencido'])
            ->assertDontSeeText('Todo en orden');
    }

    #[Test]
    public function deleting_is_soft_and_frees_the_code(): void
    {
        $asset = Asset::factory()->forCompany($this->user->company_id)->vehicle()->create(['code' => 'CAR-1']);

        $this->delete('/inventory/delete/'.$asset->id)->assertRedirect(route('asset.index', 'vehicle'));

        $this->assertSoftDeleted($asset);
        $this->post('/inventory/save', $this->vehicleData(['code' => 'CAR-1']))->assertSessionHasNoErrors();
    }

    #[Test]
    public function every_route_requires_its_permission(): void
    {
        $asset = Asset::factory()->forCompany($this->user->company_id)->vehicle()->create();
        $reader = User::factory()->create(['company_id' => $this->user->company_id]);
        $reader->givePermissionTo(Permission::findOrCreate('read asset', 'web'));
        $stranger = User::factory()->create(['company_id' => $this->user->company_id]);

        $this->actingAs($stranger)->get('/inventory/vehicle')->assertForbidden();
        $this->actingAs($stranger)->get('/inventory/show/'.$asset->id)->assertForbidden();
        $this->actingAs($stranger)->get('/')->assertDontSee(url('/inventory/vehicle'));

        $this->actingAs($reader)->get('/inventory/vehicle')->assertOk()->assertDontSee('/inventory/vehicle/create');
        $this->actingAs($reader)->get('/inventory/show/'.$asset->id)->assertOk();
        $this->actingAs($reader)->get('/inventory/vehicle/create')->assertForbidden();
        $this->actingAs($reader)->get('/inventory/update/'.$asset->id)->assertForbidden();
        $this->actingAs($reader)->post('/inventory/save', $this->vehicleData())->assertForbidden();
        $this->actingAs($reader)->post('/inventory/save', $this->vehicleData(['id' => $asset->id]))->assertForbidden();
        $this->actingAs($reader)->delete('/inventory/delete/'.$asset->id)->assertForbidden();
        $this->actingAs($reader)->post('/inventory/assign/'.$asset->id, ['user_id' => $reader->id])->assertForbidden();
        $this->actingAs($reader)->post('/inventory/return/'.$asset->id)->assertForbidden();
        $this->actingAs($reader)->get('/inventory/maintenance/create/'.$asset->id)->assertForbidden();
        $this->actingAs($reader)->get('/')->assertSee(url('/inventory/vehicle'));
    }

    #[Test]
    public function the_detail_shows_the_age_of_the_asset(): void
    {
        $this->travelTo('2026-11-21');
        $dated = Asset::factory()->forCompany($this->user->company_id)->device()->create(['purchase_date' => '2023-05-03']);
        $undated = Asset::factory()->forCompany($this->user->company_id)->device()->create(['purchase_date' => null]);

        $this->get(route('asset.show', $dated->id))->assertOk()->assertSeeText(__('Age'))->assertSeeText('3 years 6 months')->assertSeeText('03/05/2023');
        $this->get(route('asset.show', $undated->id))->assertOk()->assertSeeText(__('Unknown, add the purchase date'));

        $usedCar = Asset::factory()->forCompany($this->user->company_id)->vehicle(AssetType::Car, ['manufacturing_year' => 2006])->create(['purchase_date' => '2019-10-01']);
        $this->get(route('asset.show', $usedCar->id))->assertOk()->assertSeeText('20 years 10 months')->assertSeeText(__('manufactured in').' 2006');
    }
}
