<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Asset;

use App\Enums\AssetType;
use App\Models\Asset;
use App\Models\User;
use App\Services\AssetCodeService;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AssetCodeLabelTest extends TestCase
{
    #[Test]
    public function it_generates_the_next_code_of_the_type_counting_deleted_assets(): void
    {
        $this->getJson('/inventory/code/next?type=mobile')->assertOk()->assertExactJson(['code' => 'MOB-00001']);

        Asset::factory()->forCompany($this->user->company_id)->device(AssetType::Mobile)->create(['code' => 'MOB-00003']);
        Asset::factory()->forCompany($this->user->company_id)->device(AssetType::Mobile)->create(['code' => 'MOB-00007'])->delete();
        Asset::factory()->forCompany($this->user->company_id)->device(AssetType::Mobile)->create(['code' => 'MOB-SPARE']);
        Asset::factory()->device(AssetType::Mobile)->create(['code' => 'MOB-00050']);

        $this->getJson('/inventory/code/next?type=mobile')->assertExactJson(['code' => 'MOB-00008']);
        $this->getJson('/inventory/code/next?type=crane')->assertExactJson(['code' => 'CRN-00001']);
    }

    #[Test]
    public function generating_a_code_needs_a_valid_type_and_permission(): void
    {
        $this->getJson('/inventory/code/next')->assertUnprocessable()->assertJsonValidationErrors('type');
        $this->getJson('/inventory/code/next?type=boat')->assertUnprocessable()->assertJsonValidationErrors('type');

        $reader = User::factory()->create(['company_id' => $this->user->company_id]);
        $reader->givePermissionTo(Permission::findOrCreate('read asset', 'web'));
        $this->actingAs($reader)->getJson('/inventory/code/next?type=mobile')->assertForbidden();
    }

    #[Test]
    public function codes_only_accept_barcode_friendly_characters(): void
    {
        $data = ['category' => 'vehicle', 'type' => 'car', 'name' => 'Coche'];

        $this->post('/inventory/save', [...$data, 'code' => 'CAR 1'])->assertSessionHasErrors('code');
        $this->post('/inventory/save', [...$data, 'code' => 'CAR_1'])->assertSessionHasErrors('code');
        $this->post('/inventory/save', [...$data, 'code' => 'car-00001'])->assertSessionHasNoErrors();

        $this->assertSame('CAR-00001', Asset::firstOrFail()->code);
    }

    #[Test]
    public function searching_an_exact_code_opens_the_asset_whatever_the_category(): void
    {
        $mobile = Asset::factory()->forCompany($this->user->company_id)->device(AssetType::Mobile)->create(['code' => 'MOB-00001', 'name' => 'Móvil ventas']);
        Asset::factory()->forCompany($this->user->company_id)->device(AssetType::Mobile)->create(['code' => 'MOB-00002', 'name' => 'Móvil taller']);
        Asset::factory()->device(AssetType::Mobile)->create(['code' => 'MOB-00009']);

        $this->get('/inventory/vehicle?search=mob-00001')->assertRedirect(route('asset.show', $mobile->id));
        $this->get('/inventory/device?search='.urlencode(route('asset.code', 'MOB-00001')))->assertRedirect(route('asset.show', $mobile->id));
        $this->get('/inventory/device?search=MOB-0000')->assertOk()->assertSeeText('Móvil ventas')->assertSeeText('Móvil taller');
        $this->get('/inventory/device?search=MOB-00009')->assertOk();
    }

    #[Test]
    public function the_qr_url_opens_the_asset_of_the_company_only(): void
    {
        $asset = Asset::factory()->forCompany($this->user->company_id)->vehicle()->create(['code' => 'CRN-00001']);
        Asset::factory()->vehicle()->create(['code' => 'CRN-00002']);

        $this->get(route('asset.code', 'crn-00001'))->assertRedirect(route('asset.show', $asset->id));
        $this->get(route('asset.code', 'CRN-00002'))->assertNotFound();
    }

    #[Test]
    public function it_prints_the_label_as_barcode_or_qr(): void
    {
        $asset = Asset::factory()->forCompany($this->user->company_id)->vehicle()->create(['code' => 'CRN-00001', 'name' => 'Grúa torre']);

        $this->get(route('asset.show', $asset->id))->assertSee(route('asset.label', [$asset->id, 'format' => 'qr']), false);

        $this->get(route('asset.label', [$asset->id, 'format' => 'barcode']))
            ->assertOk()
            ->assertSee('<svg', false)
            ->assertSee('label--barcode', false)
            ->assertSeeText('CRN-00001')
            ->assertSeeText('Grúa torre');

        $this->get(route('asset.label', [$asset->id, 'format' => 'qr']))
            ->assertOk()
            ->assertSee('<svg', false)
            ->assertSee('label--qr', false)
            ->assertDontSee('<?xml', false);

        $reader = User::factory()->create(['company_id' => $this->user->company_id]);
        $reader->givePermissionTo(Permission::findOrCreate('read asset', 'web'));
        $this->actingAs($reader)->get(route('asset.label', $asset->id))->assertOk()->assertSee('label--barcode', false);
    }

    #[Test]
    public function the_label_of_another_company_is_not_found(): void
    {
        $other = Asset::factory()->vehicle()->create();

        $this->get(route('asset.label', $other->id))->assertNotFound();
    }

    #[Test]
    public function a_scan_is_read_as_a_plain_code_or_as_the_qr_url(): void
    {
        $service = new AssetCodeService;

        $this->assertSame('NB-00001', $service->codeFromScan(' nb-00001 '));
        $this->assertSame('NB-00001', $service->codeFromScan('https://crm.example.com/inventory/code/NB-00001'));
        $this->assertSame('NOTEBOOK', $service->codeFromScan('notebook'));
    }

    #[Test]
    public function hand_typed_huge_sequences_do_not_break_the_generator(): void
    {
        Asset::factory()->forCompany($this->user->company_id)->device(AssetType::Mobile)->create(['code' => 'MOB-9223372036854775807']);
        Asset::factory()->forCompany($this->user->company_id)->device(AssetType::Mobile)->create(['code' => 'MOB-00041']);

        $this->getJson('/inventory/code/next?type=mobile')->assertExactJson(['code' => 'MOB-00042']);
    }

    #[Test]
    public function the_code_cannot_change_once_the_asset_exists(): void
    {
        $asset = Asset::factory()->forCompany($this->user->company_id)->vehicle()->create(['code' => 'CAR-00001', 'name' => 'Coche']);
        $data = ['id' => $asset->id, 'category' => 'vehicle', 'type' => 'car', 'name' => 'Coche renombrado'];

        $this->get('/inventory/update/'.$asset->id)->assertOk()->assertSee('readonly', false)->assertDontSee('id="generate-code"', false);
        $this->get('/inventory/vehicle/create')->assertOk()->assertSee('id="generate-code"', false);

        $this->post('/inventory/save', [...$data, 'code' => 'CUSTOM'])->assertSessionHasErrors('code');
        $this->post('/inventory/save', [...$data, 'code' => 'car-00001'])->assertSessionHasNoErrors();

        $asset->refresh();
        $this->assertSame('CAR-00001', $asset->code);
        $this->assertSame('Coche renombrado', $asset->name);
    }
}
